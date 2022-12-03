<?php

namespace Payum\Checkoutcom\Checkout;

use Checkout\HttpClientBuilderInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HttpClientBuilder implements HttpClientBuilderInterface
{
    private $client;

    public function __construct()
    {
        $this->client = HttpClient::create();
    }

    public function getClient(): HttpClientInterface
    {
        return $this->client;
    }
}
