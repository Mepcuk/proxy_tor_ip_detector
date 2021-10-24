<?php

namespace App\Controller;

use App\Entity\TorNetworkIpList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProxyCheckController extends AbstractController
{

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }


    /**
     * Check Ip, Proxy, Tor
     *
     * @Route("/ipcheck", name="proxy_check")
     */

    public function index(Request $request): Response
    {
        $torNetwork     = false;
        $twigParameters = [
            'HTTP_ACCEPT_LANGUAGE', 'HTTP_USER_AGENT'
        ];
        $ipParameters   = [
            'REMOTE_ADDR', 'REMOTE_PORT'
        ];

        $headerData[] = '----------------------- Header Data -----------------------';
        $headers = $request->headers->all();

        foreach ($headers as $key => $headersArray) {

            $value = '';
            if (is_array($headersArray)) {
                foreach ($headersArray as $headersData) {
                    $value .= ' ' . $headersData;
                }
            }
            $headerData[] = $key . ' - ' . $value;
        }


        $dataBrowser = [];
        $dataIp = [];
        $server = [];
        foreach ($_SERVER as $key => $value) {
            $server[$key] = $value;
        }

        foreach ($twigParameters as $twigParameter) {
            if (array_key_exists($twigParameter, $server)) {
                $dataBrowser[$twigParameter] = $server[$twigParameter];
            }
        }

        foreach ($ipParameters as $ipParameter) {
            if (array_key_exists($ipParameter, $server)) {
                $dataIp[$ipParameter] = $server[$ipParameter];
            }
        }

        /**
         * Tor check
         */
        if ( key_exists('REMOTE_ADDR', $dataIp) ) {
            $torNetwork = $this->checkIpTorNetwork($dataIp['REMOTE_ADDR']);
        }

        return $this->render('proxy.html.twig', [
            'data'          => $server,
            'ip'            => $dataIp,
            'torNetwork'    => $torNetwork,
        ]);
        return $this->json($data);
    }


    /**
     * Get Tor active IP list and save to DB
     * please do not often request crontab -> /10 * * *
     * Tor List updated once in 10 min
     *
     * @Route("/toriplist", name="tor_ip_list")
     */

    public function toriplist(Request $request): Response
    {
        $response = $this->httpClient->request(
            'GET',
            'https://openinternet.io/tor/tor-exit-list.txt'
        );

        $statusCode = $response->getStatusCode();

        if (200 == $statusCode) {
            $content    = $response->getContent();
            $ipList     = $this->validateIp($content);
            $this->saveTorList($ipList);
        }


        return $this->json($content);
    }

    public function saveTorList(array $ipList):bool
    {
        if ( !empty($ipList) ) {

            $entityManager = $this->getDoctrine()->getManager();

            foreach ( $ipList as $ip ) {

                $ipRecord = $entityManager->getRepository(TorNetworkIpList::class)->findOneBy([
                    'ip' => $ip,
                ]);

                if ( !$ipRecord ) {
                    $ipRecord = new TorNetworkIpList();
                    $ipRecord->setIp($ip);
                    $ipRecord->setCreatedAt(new \DateTimeImmutable());
                    $ipRecord->setActive(true);
                }
                $ipRecord->setActive(true);

                $entityManager->persist($ipRecord);
                $entityManager->flush();
            }

            /**
             * Deactivate Tor IP - not active anymore
             */

            $ipRecords = $entityManager->getRepository(TorNetworkIpList::class)->findAll();

            /** @var TorNetworkIpList $ipRecord */
            foreach ( $ipRecords as $ipRecord ) {

                if ( !in_array($ipRecord->getIp(), $ipList) && $ipRecord->getActive() ) {
                    $ipRecord->setActive(false);
                    $ipRecord->setEndedAt(new \DateTimeImmutable());

                    $entityManager->persist($ipRecord);
                    $entityManager->flush();
                }
            }

            return true;
        } else {
            return false;
        }
    }


    /**
     * Check if proxy is alive just opening connection to Ip:port (Before use)
     *
     * @param string $ip
     * @param string $port
     * @param int $errorCode
     * @param string $errorDescription
     * @return bool
     */

    private function checkProxyAlive(string $ip, string $port, int $errorCode, string $errorDescription): boolean
    {
        try {
            $socketConnection = fsockopen($ip, $port, $errorCode, $errorDescription, 10);
            fclose($socketConnection);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }


    /**
     * Explore text by new line and Validate Ip by ipv4 or ipv6
     *
     * @param string $incomingText
     * @return array
     */

    public function validateIp(string $incomingText): array
    {
        $rowRecords = explode(PHP_EOL, $incomingText);
        $ipRecords = [];

        foreach ($rowRecords as $rowRecord) {
            if (filter_var($rowRecord, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $ipRecords[] = $rowRecord;
            }
            if (filter_var($rowRecord, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $ipRecords[] = $rowRecord;
            }
        }

        /**
         * Validate an IP address is not in a private range
         */
//        filter_var('127.0.0.1', FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
        /**
         * Validate an IP address is not in a reserved range
         */
//        filter_var('127.0.0.1', FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE)

        return $ipRecords;
    }

    /**
     * Check in Database if IP exist in active Tor Network list
     *
     * @param string $REMOTE_ADDR
     * @return bool
     */

    private function checkIpTorNetwork(string $REMOTE_ADDR):bool
    {
        $entityManager  = $this->getDoctrine()->getManager();
        $ipRecord       = $entityManager->getRepository(TorNetworkIpList::class)->findOneBy([
            'ip' => $REMOTE_ADDR,
        ]);

        return (bool)$ipRecord;
    }
}

