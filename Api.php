<?php

declare(strict_types=1);

namespace Payum\Checkoutcom;

use Checkout\CheckoutApi;
use Checkout\CheckoutSdk;
use Checkout\Environment;
use Checkout\HttpClientBuilder;

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
        return CheckoutSdk::builder()->staticKeys()
            ->secretKey($this->options['secrety_key'])
            ->environment($this->options['environment'] === self::PRODUCTION ? Environment::production() : Environment::sandbox())
            ->httpClientBuilder(new HttpClientBuilder())
            ->build();
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
