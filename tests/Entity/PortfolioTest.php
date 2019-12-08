<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Portfolio;

class PortfolioTest extends TestCase
{
    public function testSlugify()
    {
        $portfolio = $this->createPartialMock(Portfolio::class, ['setSlug']);
        $portfolio->expects($this->once())
            ->method('setSlug')
            ->with('Test-Portfolio')
        ;

        $portfolio->setName('Test Portfolio');
        $portfolio->slugify();
    }
}
