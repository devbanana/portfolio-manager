<?php

declare(strict_types = 1);

namespace App\Util\IexCloud;

use Symfony\Component\HttpClient\Exception\JsonException;

abstract class IexEndpoint
{
    protected $urlParts = [];
    protected $parameters = [];
    protected $client;

    public function __construct(IexCloudClient $client)
    {
        $this->client = $client;
    }

    protected function addUrlPart($part): self
    {
        $this->urlParts[] = $part;

        return $this;
    }

    protected function addParameter($parameter, $value): self
    {
        $this->parameters[$parameter] = $value;

        return $this;
    }

    public function __call(string $method, array $arguments = []): self
    {
        // If arguments is empty, it is probably a URL part, not query parameter
        if (empty($arguments)) {
            $this->addUrlPart($method);
        } else {
            $value = $arguments[0];

            // Booleans must be sent as a literal string
            if ($value === true) {
                $value = 'true';
            } elseif ($value === false) {
                $value = 'false';
            }

            $this->addParameter($method, $value);
        }

        return $this;
    }

    public function send()
    {
        $url = implode('/', $this->urlParts);
        $options = ['query' => $this->parameters];
        $response = $this->client->request('GET', $url, $options);

        // Try to convert to JSON
        try {
            return $response->toArray();
        } catch (JsonException $e) {
            return $response->getContent();
        }
    }
}
