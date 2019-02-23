<?php

declare(strict_types=1);

namespace Payum\Checkoutcom\Action;

use Checkout\CheckoutApi;
use Checkout\Library\Exceptions\CheckoutException;
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

        /** @var CheckoutApi $checkoutApiClient */
        $checkoutApi = $this->api->getCheckoutApi();

        $capture = new Capture($model['id']);
        $capture->amount = $model['amount'];

        try {
            $details = $checkoutApi->payments()->capture($capture);
        } catch (CheckoutException $e) {
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
