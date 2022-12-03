<?php

declare(strict_types=1);

namespace Payum\Checkoutcom\Action;

use Checkout\Previous\CheckoutApi;
use Checkout\CheckoutApiException;
use Checkout\Payments\RefundRequest;
use Payum\Checkoutcom\Action\Api\BaseApiAwareAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Refund;

class RefundAction extends BaseApiAwareAction
{
    use GatewayAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @param Refund $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        $model->validateNotEmpty(['id', 'amount']);

        /** @var CheckoutApi $checkoutApi */
        $checkoutApi = $this->api->getCheckoutApi();

        $refundRequest = new RefundRequest();
        $refundRequest->amount = (int) $model['amount'];

        try {
            $details = $checkoutApi->getPaymentsClient()->refundPayment($model['id'], $refundRequest);
        } catch (CheckoutApiException $e) {
            throw new \InvalidArgumentException($e->getMessage(), $e->getCode());
        }

        $model->replace((array) $details);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Refund &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
