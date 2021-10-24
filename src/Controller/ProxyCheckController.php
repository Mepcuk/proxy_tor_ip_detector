<?php

namespace App\Controller;

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

        $twigParameters = [
            'HTTP_ACCEPT_LANGUAGE', 'HTTP_USER_AGENT'
        ];
        $ipParameters = [
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

        return $this->render('proxy.html.twig', [
            'data' => $server,
            'ip' => $dataIp,
        ]);
        return $this->json($data);
    }


    /**
     * Get Tor active IP list and save to DB
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
            $content = $response->getContent();
            $ipList = $this->validateIp($content);
        }


        return $this->json($content);
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
}

