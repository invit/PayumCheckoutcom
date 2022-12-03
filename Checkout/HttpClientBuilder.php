<?php

namespace Payum\Checkoutcom\Checkout;

use Checkout\HttpClientBuilderInterface;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\HttpClient\Psr18Client;

final class HttpClientBuilder implements HttpClientBuilderInterface
{
    private $client;

    public function __construct()
    {
        $this->client = new Psr18Client();
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }
}
