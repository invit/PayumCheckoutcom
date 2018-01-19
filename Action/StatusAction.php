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
        if (in_array($model['responseCode'], [10000, 10100, 10200])) {
            switch ($model['status']) {
                case 'Authorised':
                    $request->markAuthorized();
                    break;
                case 'Captured':
                    $request->markCaptured();
                    break;
                case 'Voided':
                    $request->markCanceled();
                    break;
                case 'Refunded':
                    $request->markRefunded();
                    break;
                default:
                    $request->markUnknown();
                    break;
            }

            return;
        }


        if (is_int($model['responseCode']) && $model['responseCode'] >= 20000 && $model['responseCode'] < 50000) {
            $request->markFailed();
            return;
        }

        $request->markUnknown();
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
