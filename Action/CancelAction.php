<?php
namespace Payum\Checkoutcom\Action;

use com\checkout\ApiServices\Charges\RequestModels\ChargeVoid;
use com\checkout\helpers\ApiHttpClientCustomException;
use Payum\Checkoutcom\Action\Api\BaseApiAwareAction;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Cancel;

class CancelAction extends BaseApiAwareAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param Cancel $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        $model->validateNotEmpty(['chargeId', 'trackId']);

        $checkoutApiClient = $this->api->getCheckoutApiClient();
        $chargeService = $checkoutApiClient->chargeService();

        $chargePayload = new ChargeVoid();
        $chargePayload->setTrackId($model['trackId']);

        try {
            $chargeResponse = $chargeService->voidCharge($model['chargeId'], $chargePayload);

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
            $request instanceof Cancel &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
