<?php

declare(strict_types=1);

namespace Payum\Checkoutcom\Action;

use Checkout\CheckoutApi;
use Checkout\CheckoutApiException;
use Payum\Checkoutcom\Action\Api\BaseApiAwareAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Cancel;

class CancelAction extends BaseApiAwareAction
{
    use GatewayAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @param Cancel $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        $model->validateNotEmpty(['id']);

        /** @var CheckoutApi $checkoutApi */
        $checkoutApi = $this->api->getCheckoutApi();

        try {
            $details = $checkoutApi->getPaymentsClient()->voidPayment($model['id']);
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
            $request instanceof Cancel &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
