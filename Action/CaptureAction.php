<?php

declare(strict_types=1);

namespace Payum\Checkoutcom\Action;

use Checkout\CheckoutApi;
use Checkout\CheckoutApiException;
use Checkout\Payments\CaptureRequest;
use Payum\Checkoutcom\Action\Api\BaseApiAwareAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Exception\RequestNotSupportedException;

class CaptureAction extends BaseApiAwareAction
{
    use GatewayAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        $model->validateNotEmpty(['id', 'amount']);

        /** @var CheckoutApi $checkoutApi */
        $checkoutApi = $this->api->getCheckoutApi();

        $caputureRequest = new CaptureRequest();
        $caputureRequest->amount = (int) $model['amount'];

        try {
            $details = $checkoutApi->getPaymentsClient()->capturePayment($model['id'], $caputureRequest);
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
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
