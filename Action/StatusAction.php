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

        if (!isset($model['STATUS']) || !strlen($model['STATUS'])) {
            $request->markNew();

            return;
        }

        // details on the status codes: https://docs.checkout.com/reference/response-codes
        switch ($model['STATUS']) {
            case 10000:
            case 10100:
            case 10200:
                $request->markAuthorized();
                break;
            default:
                if (is_int($model['STATUS']) && $model['STATUS'] >= 20000 && $model['STATUS'] < 50000) {
                    $request->markFailed();
                }

                $request->markUnknown();
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
