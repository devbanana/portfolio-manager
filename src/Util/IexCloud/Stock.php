<?php

declare(strict_types = 1);

namespace App\Util\IexCloud;

class Stock extends IexEndpoint
{
    private $symbol;

    public function __construct(string $symbol, IexCloudClient $client)
    {
        $this->symbol = $symbol;
        $this->client = $client;
        $this->addUrlPart('stock')
            ->addUrlPart($symbol);
    }

    public function getClient(): IexCloudClient
    {
        return $this->client;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function quote($field = null): self
    {
        $this->addUrlPart('quote');
        if ($field) {
            $this->addUrlPart($field);
        }

        return $this;
    }
}
