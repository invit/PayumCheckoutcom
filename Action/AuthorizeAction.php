<?php
namespace Payum\Checkoutcom\Action;

use com\checkout\ApiServices\Charges\RequestModels\CardTokenChargeCreate;
use com\checkout\ApiServices\SharedModels\Address;
use com\checkout\helpers\ApiHttpClientCustomException;
use Payum\Checkoutcom\Action\Api\BaseApiAwareAction;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\InvalidArgumentException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Authorize;
use Payum\Core\Exception\RequestNotSupportedException;

class AuthorizeAction extends BaseApiAwareAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param Authorize $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);
        $model = ArrayObject::ensureArrayObject($request->getModel());
        $model->validateNotEmpty(['amount', 'currency', 'trackId', 'cardToken']);

        $checkoutApiClient = $this->api->getCheckoutApiClient();
        $chargeService = $checkoutApiClient->chargeService();

        $cardTokenChargePayload = new CardTokenChargeCreate();
        $cardTokenChargePayload->setEmail($model['customerEmail']);
        $cardTokenChargePayload->setAutoCapture($model['autoCapture'] === true ? 'Y' : 'N');
        $cardTokenChargePayload->setValue($model['amount']);
        $cardTokenChargePayload->setCurrency($model['currency']);
        $cardTokenChargePayload->setTrackId($model['trackId']);
        $cardTokenChargePayload->setCardToken($model['cardToken']);

        try {
            $chargeResponse = $chargeService->chargeWithCardToken($cardTokenChargePayload);
        } catch (ApiHttpClientCustomException $e) {
            throw new InvalidArgumentException($e->getErrorMessage(), $e->getCode());
        }

        $model['STATUS'] = $chargeResponse->getResponseCode();

        return $model;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Authorize &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
