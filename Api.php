<?php
namespace Payum\Checkoutcom;

use com\checkout\ApiClient;
use Http\Message\MessageFactory;
use Payum\Core\Exception\Http\HttpException;
use Payum\Core\HttpClientInterface;

class Api
{
    const TEST = 'test';
    const PRODUCTION = 'production';

    /**
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param array               $options
     * @param HttpClientInterface $client
     * @param MessageFactory      $messageFactory
     *
     * @throws \Payum\Core\Exception\InvalidArgumentException if an option is invalid
     */
    public function __construct(array $options, HttpClientInterface $client, MessageFactory $messageFactory)
    {
        $this->options = $options;
        $this->client = $client;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @return ApiClient
     */
    public function getCheckoutApiClient()
    {
        $env = 'sandbox';

        if ($this->options['environment'] === self::PRODUCTION) {
            $env =' live';
        }

        return new ApiClient($this->options['secrety_key'], $env);
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
