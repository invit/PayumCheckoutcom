<?php

declare(strict_types=1);

namespace Payum\Checkoutcom\Action;

use Checkout\Previous\CheckoutApi;
use Checkout\CheckoutApiException;
use Payum\Checkoutcom\Action\Api\BaseApiAwareAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\InvalidArgumentException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Sync;

class SyncAction extends BaseApiAwareAction
{
    /**
     * {@inheritdoc}
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        $model->validateNotEmpty(['id']);

        /** @var CheckoutApi $checkoutApi */
        $checkoutApi = $this->api->getCheckoutApi();

        try {
            $details = $checkoutApi->getPaymentsClient()->getPaymentDetails($model['id']);
        } catch (CheckoutApiException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode());
        }

        $model->replace((array) $details);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Sync &&
            $request->getModel() instanceof \ArrayAccess
            ;
    }
}
