<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DslControllerTest extends WebTestCase
{
    public function testSomething()
    {
        $fn = "+";
        $a = "4";
        $b = "ebitda";

        $requestParams = [
            "expression" => ["fn" => $fn, "a" => $a, "b" => $b],
            "security" => "CDE"
        ];


        $client = static::createClient();
        $client->request(
            'POST',
            '/dsl',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"expression": {"fn": "*", "a": "sales", "b": 2},"security": "ABC"}'
        );

        //ABC sales = 4, 4 * 2 = 8
        $this->assertJson('{"message":"OK","value":8}', $client->getResponse()->getContent());

        $client->request(
            'POST',
            '/dsl',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"expression": {"fn": "+", "a": "sales", "b": 2},"security": "ABC"}'
        );

        //ABC sales = 4, 4 + 2 = 6
        $this->assertJson('{"message":"OK","value":6}', $client->getResponse()->getContent());

        $client->request(
            'POST',
            '/dsl',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"expression": {"fn": "+", "a": "5", "b": 2},"security": "ABC"}'
        );

        //5 + 2 = 7
        $this->assertJson('{"message":"OK","value":7}', $client->getResponse()->getContent());
    }
}
