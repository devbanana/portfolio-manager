<?php

namespace App\Tests\Util\IexCloud;

use PHPUnit\Framework\TestCase;
use App\Util\IexCloud\IexCloudClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class StockTest extends TestCase
{
    protected function initializeClient($response)
    {
        $httpClient = new MockHttpClient($response);
        return new IexCloudClient('key', 'secret', 'stable', $httpClient);
    }

    public function testQuote()
    {
        $client = $this->initializeClient(function ($method, $url, $options) {
            $this->assertContains('stock/AAPL/quote', $url);
            $this->assertArrayHasKey('query', $options);
            $this->assertArrayHasKey('token', $options['query']);
            $this->assertEquals('key', $options['query']['token']);
            $body = '{"symbol":"AAPL","companyName":"Apple, Inc.","latestPrice":267.25}';
            return new MockResponse($body);
        });

        $quote = $client
            ->stock('AAPL')
            ->quote()
            ->send()
        ;

        $this->assertArrayHasKey('symbol', $quote);
        $this->assertEquals('AAPL', $quote['symbol']);
    }

    public function testQuoteWithField()
    {
        $client = $this->initializeClient(function ($method, $url, $options) {
            $this->assertContains('stock/AAPL/quote/latestPrice', $url);
            return new MockResponse(267.25);
        });

        $quote = $client
            ->stock('AAPL')
            ->quote('latestPrice')
            ->send()
        ;

        $this->assertEquals(267.25, $quote);
    }

    public function testQuoteWithQuery()
    {
        $client = $this->initializeClient(function ($method, $url, $options) {
            $this->assertContains('stock/AAPL/quote/changePercent', $url);
            $this->assertArrayHasKey('displayPercent', $options['query']);
            $this->assertSame('true', $options['query']['displayPercent']);
            return new MockResponse(0.3);
        });

        $quote = $client
            ->stock('AAPL')
            ->quote('changePercent')
            ->displayPercent(true)
            ->send()
        ;

        $this->assertEquals(0.3, $quote);
    }
}
