<?php
namespace Payum\Checkoutcom;

use Payum\Checkoutcom\Action\AuthorizeAction;
use Payum\Checkoutcom\Action\CancelAction;
use Payum\Checkoutcom\Action\ConvertPaymentAction;
use Payum\Checkoutcom\Action\CaptureAction;
use Payum\Checkoutcom\Action\NotifyAction;
use Payum\Checkoutcom\Action\RefundAction;
use Payum\Checkoutcom\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class CheckoutcomGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'checkoutcom',
            'payum.factory_title' => 'checkoutcom',
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
                'environment' => Api::TEST
            );
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = ['api_key'];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);
                $checkoutcomConfig = [
                    'api_key' => $config['api_key'],
                    'environment' => $config['environment'],
                ];

                return new Api($checkoutcomConfig, $config['payum.http_client'], $config['httplug.message_factory']);
            };
        }
    }
}
