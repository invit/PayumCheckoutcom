<?php

declare(strict_types=1);

namespace Payum\Checkoutcom;

use Checkout\CheckoutApi;

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
        $sandbox = $this->options['environment'] === self::PRODUCTION ? -1 : 1;

        return new CheckoutApi($this->options['secrety_key'], $sandbox);
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
