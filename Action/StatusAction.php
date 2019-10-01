<?php

declare(strict_types=1);

namespace Payum\Checkoutcom\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;

class StatusAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     *
     * @param GetStatusInterface $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (!isset($model['status']) || !strlen($model['status'])) {
            $request->markNew();

            return;
        }

        // details on the status codes: https://docs.checkout.com/v2.0/docs/response-codes
        switch ($model['status']) {
            case 'Authorized':
                $request->markAuthorized();
                break;
            case 'Partially Captured':
            case 'Captured':
                $request->markCaptured();
                break;
            case 'Voided':
                $request->markCanceled();
                break;
            case 'Refunded':
                $request->markRefunded();
                break;
            case 'Pending':
                $request->markPending();
                break;
            case 'Declined':
                $request->markFailed();
                break;
            default:
                $request->markUnknown();
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
