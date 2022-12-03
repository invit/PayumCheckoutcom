<?php

namespace Payum\Checkoutcom\Checkout;

use Symfony\Component\HttpClient\HttpClient;

final class HttpClientBuilder implements HttpClientBuilderInterface
{
    private $client;

    public function __construct()
    {
        $this->client = new HttpClient();
    }

    /**
     * @return HttpClient
     */
    public function getClient(): HttpClient
    {
        return $this->client;
    }
}
