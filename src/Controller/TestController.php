<?php
/**
 * Created by PhpStorm.
 * User: sidorenko
 * Date: 07.05.19
 * Time: 18:49
 */

namespace App\Controller;


use GuzzleHttp\Client;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{
    /**
     * @Route("/test")
     */
    public function number()
    {
        $client = new \Goutte\Client();
        $guzzle = new Client();
        $res = $guzzle->request('GET', 'http://forum.domik.ua/sitemap.xml');
        /** @var \SimpleXMLElement $response */
        $response = simplexml_load_string($res->getBody()->getContents());


        $json_string = json_encode($response);
        $result_array = json_decode($json_string, TRUE);

        $ress = [];
        foreach ($result_array as $result) {
            foreach ($result as $url) {
                if(strpos($url['loc'], '/zhk') !== false) {
                    $ress[] = $url['loc'];
                }

                if(strpos($url['loc'], '/zhi') !== false) {
                    $ress[] = $url['loc'];
                }
            }

        }
        $resA = [];
        $count = 0;
        foreach ($ress as $url) {
            $count++;
            if($count > 10) {
                break;
            }
            try {
                $cr = $client->request('GET', $url);

                $name = $cr->filter('#page-body')->filter('.title')->filter('a')->text();

                foreach ($cr->filter('.polls dl') as $i => $node) {
                    $resA[$count]['url'] = $url;
                    $resA[$count]['name'] = $name;
                    if($i < 5) {
                        $scoreName = $node->getElementsByTagName('dt')->item(0)->textContent;
                        $number = $node->getElementsByTagName('dd')->item(0)->getElementsByTagName('div')->item(0)->textContent;
                        $resA[$count]['score'][$i] = $number;

                    }
                }
            } catch (\Exception $e ){

            }

        }

echo "<pre>";
        print_r($resA);


        die('121ddsdl');
       die('dd');
    }
}