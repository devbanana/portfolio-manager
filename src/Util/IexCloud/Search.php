<?php

declare(strict_types = 1);

namespace App\Util\IexCloud;

class Search extends IexEndpoint
{
    private $fragment;

    public function __construct(string $fragment, IexCloudClient $client)
    {
        parent::__construct($client);
        $this->fragment = $fragment;
        $this->addUrlPart('search')
            ->addUrlPart($fragment);
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }
}
