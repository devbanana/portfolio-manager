<?php

namespace App\Tests\Twig;

use PHPUnit\Framework\TestCase;
use App\Twig\PortfolioExtension;

class PortfolioExtensionTest extends TestCase
{
    private $ext;

    public function setup()
    {
        $this->ext = new PortfolioExtension();
    }

    public function testMillidate()
    {
        $ts = time();
        $date = new \DateTime("@$ts");
        $this->assertEquals($date->format('Y-m-d H:i:s'), $this->ext->millidate($ts*1000, 'Y-m-d H:i:s'));
        $this->assertEquals(0, $this->ext->millidate($ts*1000, 'u'));
    }

    public function testMillidateWithRealMilliseconds()
    {
        $ts = microtime(true);
        $micro = sprintf('%06d', bcmul(bcsub($ts, floor($ts), 6), 1000000, 0));
        $this->assertEquals($micro, $this->ext->millidate($ts*1000, 'u'));
    }

    public function testFormatLargeNumber()
    {
        $this->assertEquals('1,000', $this->ext->formatLargeNumber(1000));
        $this->assertEquals('485,021', $this->ext->formatLargeNumber(485021));
        $this->assertEquals('1.0M', $this->ext->formatLargeNumber(1000000));
        $this->assertEquals('1.5M', $this->ext->formatLargeNumber(1463235));
        $this->assertEquals('8.4M', $this->ext->formatLargeNumber(8351564));
        $this->assertEquals('1.0B', $this->ext->formatLargeNumber(1000000000));
        $this->assertEquals('12.4B', $this->ext->formatLargeNumber(12416189435));
        $this->assertEquals('1.0T', $this->ext->formatLargeNumber(1000000000000));
        $this->assertEquals('1.2T', $this->ext->formatLargeNumber(1187463907500));
    }

    public function testFormatLargeNumberWith2Decimals()
    {
        $this->assertEquals('1,000', $this->ext->formatLargeNumber(1000, 2));
        $this->assertEquals('485,021', $this->ext->formatLargeNumber(485021, 2));
        $this->assertEquals('1.00M', $this->ext->formatLargeNumber(1000000, 2));
        $this->assertEquals('1.46M', $this->ext->formatLargeNumber(1463235, 2));
        $this->assertEquals('8.35M', $this->ext->formatLargeNumber(8351564, 2));
        $this->assertEquals('1.00B', $this->ext->formatLargeNumber(1000000000, 2));
        $this->assertEquals('12.42B', $this->ext->formatLargeNumber(12416189435, 2));
        $this->assertEquals('1.00T', $this->ext->formatLargeNumber(1000000000000, 2));
        $this->assertEquals('1.19T', $this->ext->formatLargeNumber(1187463907500, 2));
    }
}
