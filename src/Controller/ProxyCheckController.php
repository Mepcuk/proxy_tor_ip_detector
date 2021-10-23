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

        $data[]     = '----------------------- Header Data -----------------------';
        $headers    = $request->headers->all();

        foreach ( $headers as $key => $headersArray ) {

            $value = '';
            if ( is_array($headersArray) ) {
                foreach ( $headersArray as $headersData) {
                    $value .= ' ' . $headersData;
                }
            }
            $data[] = $key . ' - ' . $value;
        }

        natsort($data);
        $data[] = '';
        $data[] = '----------------------- $_SERVER -----------------------';

        $server = [];
        foreach ( $_SERVER as $key => $value ) {
            $server[] = $key . ' - ' . $value;
        }
        asort($server);
        $data[] = $server;

        $a = 10;


        return $this->json($data);
    }
}
