<?php

namespace App\Tests\Util\IexCloud;

use PHPUnit\Framework\TestCase;
use App\Util\IexCloud\IexCloudClient;
use App\Util\IexCloud\Stock;
use Symfony\Component\HttpClient\MockHttpClient;

class IexCloudClientTest extends TestCase
{
    private $client;

    public function setup()
    {
        $this->client = new IexCloudClient('key', 'secret', 'stable', new MockHttpClient());
    }

    public function testGetStock()
    {
        $stock = $this->client->stock('AAPL');
        $this->assertInstanceOf(Stock::class, $stock);
        $this->assertEquals($this->client, $stock->getClient());
        $this->assertEquals('AAPL', $stock->getSymbol());
    }
}
