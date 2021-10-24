<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProxyCheckController extends AbstractController
{
    /**
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

        $headerData[]   = '----------------------- Header Data -----------------------';
        $headers        = $request->headers->all();

        foreach ( $headers as $key => $headersArray ) {

            $value = '';
            if ( is_array($headersArray) ) {
                foreach ( $headersArray as $headersData) {
                    $value .= ' ' . $headersData;
                }
            }
            $headerData[] = $key . ' - ' . $value;
        }


        $dataBrowser    = [];
        $dataIp         = [];
        $server         = [];
        foreach ( $_SERVER as $key => $value ) {
            $server[$key] = $value;
        }

        foreach ( $twigParameters as $twigParameter ) {
            if ( array_key_exists($twigParameter, $server)) {
                $dataBrowser[$twigParameter] = $server[$twigParameter];
            }
        }

        foreach ( $ipParameters as $ipParameter ) {
            if ( array_key_exists($ipParameter, $server)) {
                $dataIp[$ipParameter] = $server[$ipParameter];
            }
        }

        return $this->render('proxy.html.twig', [
            'data'  => $server,
            'ip'    => $dataIp,
        ]);
        return $this->json($data);
    }

    /**
     * @Route("/toriplist", name="tor_ip_list")
     */
    public function toriplist(Request $request): Response
    {


        return $this->json($data);
    }



    private function checkProxyAlive(string $ip, string $port, int $errorCode, string $errorDescription): boolean
    {
        try {
            $socketConnection = fsockopen($ip, $port, $errorCode, $errorDescription, 10);
            fclose($socketConnection);
            return true;
        } catch ( \Throwable $th) {
            return false;
        }
    }

    /**
     * https://openinternet.io/tor/tor-exit-list.txt
     */
}
