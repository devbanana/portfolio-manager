<?php

declare(strict_types = 1);

namespace App\Util\IexCloud;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpOptions;

class IexCloudClient
{
    /**
     * IEX Cloud public token
     *
     * @var string
     */
    private $key;

    /**
     * IEX Cloud secret token
     *
     * @var string
     */
    private $secret;

    /**
     * IEX Cloud API version
     *
     * @var string
     */
    private $version;

    /**
     * The HTTP client instance
     *
     * @var \Symfony\Contracts\HttpClient\HttpClientInterface
     */
    private $client;

    /**
     * The HTTP options
     *
     * @var \Symfony\Component\HttpClient\HttpOptions
     */
    private $options;

    public function __construct(string $key, string $secret, string $version, HttpClientInterface $client)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->version = $version;
        $this->client = $client;
        $this->options = new HttpOptions();
        $this->options->setBaseUri("https://cloud.iexapis.com/$version/");
    }

    public function stock(string $symbol)
    {
        return new Stock($symbol, $this);
    }

    public function search(string $fragment): Search
    {
        return new Search($fragment, $this);
    }

    /**
     * Requests the API endpoint
     *
     * Note that all URLs are relative, with the base URI https://cloud.iexapis.com/$version/
     *
     * @param string $method The request method
     * @param string $url The relative URL to call
     * @param array $options The request options
     * @param bool $secret Whether to use the secret token
     */
    public function request(string $method, string $url, array $options = [], $secret = false)
    {
        $options = array_merge($options, $this->options->toArray());
        $options['query']['token'] = $secret ? $this->secret : $this->key;
        return $this->client->request($method, $url, $options);
    }
}
