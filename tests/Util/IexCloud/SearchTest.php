<?php

namespace App\Tests\Util\IexCloud;

use PHPUnit\Framework\TestCase;
use App\Util\IexCloud\IexCloudClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class SearchTest extends TestCase
{
    protected function initializeClient($response)
    {
        $httpClient = new MockHttpClient($response);
        return new IexCloudClient('key', 'secret', 'stable', $httpClient);
    }

    public function testSearch()
    {
        $client = $this->initializeClient(function ($method, $url, $options) {
            $this->assertContains('search/AAPL', $url);
            $this->assertArrayHasKey('query', $options);
            $this->assertArrayHasKey('token', $options['query']);
            $this->assertEquals('key', $options['query']['token']);
            $body = '[{"symbol":"AAPL","securityName":"Apple Inc.","securityType":"cs","region":"US","exchange":"NAS"},{"symbol":"AAPL-MM","securityName":"Apple Inc.","securityType":"cs","region":"MX","exchange":"MEX"}]';
            return new MockResponse($body);
        });

        $search = $client
            ->search('AAPL')
            ->send()
        ;

        $this->assertCount(2, $search);
        $this->assertEquals('AAPL', $search[0]['symbol']);
        $this->assertEquals('NAS', $search[0]['exchange']);
    }
}
