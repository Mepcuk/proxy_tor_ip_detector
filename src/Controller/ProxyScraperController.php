<?php

namespace App\Controller;

use App\Entity\ProxyList;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Test\Constraint\CrawlerSelectorAttributeValueSame;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProxyScraperController extends AbstractController
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $entityManager)
    {
        $this->httpClient       = $httpClient;
        $this->entityManager    = $entityManager;
    }


    /**
     * Web Spider - https://free-proxy-list.net/
     *
     * @Route("/spider_freeproxylists", name="spider_freeproxylists")
     */

    public function scraperFreeproxylist(): Response
    {
        $proxyListUrl = 'https://www.freeproxylists.net/ru/?page=';
        $proxyListUrl = 'https://geonode.com/free-proxy-list';
        $proxyListUrl = 'https://free-proxy-list.net/';


        $response   = $this->httpClient->request(
            'GET',
            $proxyListUrl
        );

        $statusCode = $response->getStatusCode();

        if (200 == $statusCode) {
            $content    = $response->getContent();
            $crawler    = new Crawler($content);
            $tableBody  = $crawler->filter('tbody');
            $tableRows  = $crawler->filter('tbody tr')->each(function (Crawler $node, $i) {
                return $node->html();
            });

            if ( $tableRows ) {
                foreach ($tableRows as $row) {
                    $parentCrawler  = new Crawler($row);
                    $tableColumns   = $parentCrawler->filter('td')->each(function ($node) {
                        return $node->text();
                    });

                    if ( count($tableColumns) == 8 ) {
                        if (filter_var($tableColumns[0], FILTER_VALIDATE_IP)) {
                            $proxyRecord = new ProxyList();
                            $proxyRecord->setIp($tableColumns[0]);
                            $proxyRecord->setPort($tableColumns[1]);
                            $proxyRecord->setCountryCode($tableColumns[2]);
                            $proxyRecord->setCountry($tableColumns[3]);
                            $proxyRecord->setProxyAnonymity($tableColumns[4]);
                            $proxyRecord->setGoogleCheck($this->checkYesNo($tableColumns[5]));
                            $proxyRecord->setHttpsCheck($this->checkYesNo($tableColumns[6]));

                            $this->entityManager->persist($proxyRecord);
                            $this->entityManager->flush();
                        }
                    }

                    $b = 2;
                }
            }

            $a = 1;
        }

        return $this->json(json_encode($content));
    }


    /**
     * Web Spider - https://www.freeproxylists.net/ru/?page=1
     *
     * @Route("/spider_freeproxylists_chrome", name="spider_freeproxylispider_freeproxylists_chromests")
     */

    public function scraperFreeproxylistChrome(): Response
    {
        $proxyListUrl = 'https://www.freeproxylists.net/ru/?page=';
        $proxyListUrl = 'https://geonode.com/free-proxy-list';
        $proxyListUrl = 'https://free-proxy-list.net/';



        for ($i = 1; $i <= 5; $i++) {

            $proxyUrl = $proxyListUrl . $i;
            $response   = $this->httpClient->request(
                'GET',
                $proxyListUrl
            );

            $statusCode = $response->getStatusCode();

            if (200 == $statusCode) {
                $content    = $response->getContent();
                $crawler    = new Crawler($content);
                $tableBody  = $crawler->filter('tablebody');
                $tableRows  = $crawler->filter('tablebody tr');
//                $ipList     = $this->validateIp($content);
//                $this->saveTorList($ipList);
                $a = 1;
            }
        }

        return $this->json(json_encode($content));
    }

    private function checkYesNo($text):bool {
        if ( $text == 'yes' ) {
            return true;
        } else {
            return false;
        }
    }

}