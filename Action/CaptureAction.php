<?php
namespace Payum\Checkoutcom\Action;

use com\checkout\ApiServices\Charges\RequestModels\ChargeCapture;
use com\checkout\helpers\ApiHttpClientCustomException;
use Payum\Checkoutcom\Action\Api\BaseApiAwareAction;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Exception\RequestNotSupportedException;

class CaptureAction extends BaseApiAwareAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        $model->validateNotEmpty(['amount', 'chargeId']);

        $checkoutApiClient = $this->api->getCheckoutApiClient();
        $chargeService = $checkoutApiClient->chargeService();

        $chargeCapturePayload = new ChargeCapture();
        $chargeCapturePayload->setChargeId($model['chargeId']);
        $chargeCapturePayload->setValue($model['amount']);

        try {
            $chargeResponse = $chargeService->CaptureCardCharge($chargeCapturePayload);
        } catch (ApiHttpClientCustomException $e) {
            throw new InvalidArgumentException($e->getErrorMessage(), $e->getCode());
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
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
