<?php
namespace Payum\Checkoutcom\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;

class StatusAction implements ActionInterface
{
    /**
     * {@inheritDoc}
     *
     * @param GetStatusInterface $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (!isset($model['responseCode']) || !strlen($model['responseCode'])) {
            $request->markNew();

            return;
        }

        // details on the status codes: https://docs.checkout.com/reference/response-codes
        switch ($model['responseCode']) {
            case 10000:
            case 10100:
            case 10200:
                if ($model['status'] === 'Authorised') {
                    $request->markAuthorized();
                } elseif ($model['status'] === 'Captured') {
                    $request->markCaptured();
                } else {
                    $request->markUnknown();
                }

                break;
            default:
                if (is_int($model['responseCode']) && $model['responseCode'] >= 20000 && $model['responseCode'] < 50000) {
                    $request->markFailed();
                } else {
                    $request->markUnknown();
                }

                break;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
