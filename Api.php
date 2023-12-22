<?php

declare(strict_types=1);

namespace Payum\Checkoutcom;


use Checkout\CheckoutSdk;
use Checkout\Environment;
use Checkout\CheckoutApi;
use Payum\Checkoutcom\Checkout\HttpClientBuilder;

class Api
{
    const TEST = 'test';
    const PRODUCTION = 'production';

    /**
     * @var array
     */
    private $options = [];

    /**
     * @throws \Payum\Core\Exception\InvalidArgumentException if an option is invalid
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function getCheckoutApi(): CheckoutApi
    {
        return CheckoutSdk::builder()
            ->staticKeys()
            ->secretKey($this->options['secret_key'])
            ->environment($this->options['environment'] === self::PRODUCTION ? Environment::production() : Environment::sandbox())
            ->build();
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
