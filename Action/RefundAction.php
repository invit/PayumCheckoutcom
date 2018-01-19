<?php
namespace Payum\Checkoutcom\Action;

use com\checkout\ApiServices\Charges\RequestModels\ChargeRefund;
use com\checkout\helpers\ApiHttpClientCustomException;
use Payum\Checkoutcom\Action\Api\BaseApiAwareAction;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Refund;

class RefundAction extends BaseApiAwareAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param Refund $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        $model->validateNotEmpty(['chargeId', 'amount']);

        $checkoutApiClient = $this->api->getCheckoutApiClient();
        $chargeService = $checkoutApiClient->chargeService();

        $chargeCapturePayload = new ChargeRefund();
        $chargeCapturePayload->setChargeId($model['chargeId']);
        $chargeCapturePayload->setValue($model['amount']);

        try {
            $chargeResponse = $chargeService->refundCardChargeRequest($chargeCapturePayload);
        } catch (ApiHttpClientCustomException $e) {
            throw new \InvalidArgumentException($e->getErrorMessage(), $e->getCode());
        }

        $model['responseCode'] = $chargeResponse->getResponseCode();
        $model['status'] = $chargeResponse->getStatus();
        $model['chargeId'] = $chargeResponse->getId();
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Refund &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
