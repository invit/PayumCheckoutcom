<?php
namespace Payum\CheckoutcomCreditcard;

use Payum\CheckoutcomCreditcard\Action\AuthorizeAction;
use Payum\CheckoutcomCreditcard\Action\CancelAction;
use Payum\CheckoutcomCreditcard\Action\ConvertPaymentAction;
use Payum\CheckoutcomCreditcard\Action\CaptureAction;
use Payum\CheckoutcomCreditcard\Action\NotifyAction;
use Payum\CheckoutcomCreditcard\Action\RefundAction;
use Payum\CheckoutcomCreditcard\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class CheckoutcomCreditcardGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'checkoutcom_creditcard',
            'payum.factory_title' => 'checkoutcom_creditcard',
            'payum.action.capture' => new CaptureAction(),
            'payum.action.authorize' => new AuthorizeAction(),
            'payum.action.refund' => new RefundAction(),
            'payum.action.cancel' => new CancelAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
                'sandbox' => true,
            );
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new Api((array) $config, $config['payum.http_client'], $config['httplug.message_factory']);
            };
        }
    }
}
