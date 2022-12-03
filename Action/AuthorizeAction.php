<?php

declare(strict_types=1);

namespace Payum\Checkoutcom\Action;

use Checkout\Previous\CheckoutApi;
use Checkout\CheckoutApiException;
use Checkout\Payments\Request\PaymentRequest;
use Checkout\Payments\Request\Source\RequestTokenSource;
use Payum\Checkoutcom\Action\Api\BaseApiAwareAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\InvalidArgumentException;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Authorize;
use Payum\Core\Exception\RequestNotSupportedException;

class AuthorizeAction extends BaseApiAwareAction
{
    use GatewayAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @param Authorize $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);
        $model = ArrayObject::ensureArrayObject($request->getModel());
        $model->validateNotEmpty(['amount', 'currency', 'token']);

        /** @var CheckoutApi $checkoutApi */
        $checkoutApi = $this->api->getCheckoutApi();


        $requestTokenSource = new RequestTokenSource();
        $requestTokenSource->token = $model['token'];

        $paymentRequest = new PaymentRequest();
        $paymentRequest->amount = $model['amount'];
        $paymentRequest->source = $requestTokenSource;
        $paymentRequest->currency = $model['currency'];

        $optionalParameters = [
            'payment_type',
            'reference',
            'description',
            'capture',
            'capture_on',
            'customer',
            'billing_descriptor',
            'shipping',
            '3ds',
            'previous_payment_id',
            'risk',
            'success_url',
            'failure_url',
            'payment_ip',
            'recipient',
            'metadata',
        ];

        foreach ($optionalParameters as $parameter) {
            if (isset($model[$parameter])) {
                $paymentRequest->{$parameter} = is_array($model[$parameter]) ? (object) $model[$parameter] : $model[$parameter];
            }
        }

        try {
            $details = $checkoutApi->getPaymentsClient()->requestPayment($paymentRequest);
            $model->replace((array) $details);
        } catch (CheckoutApiException $e) {
            $model['error'] = $e->error_details;
            throw new InvalidArgumentException($e->getMessage(), $e->getCode());
        }

        if ($model['http_code'] === 202) {
            throw new HttpRedirect($model['_links']['redirect']['href']);
        }

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Authorize &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
