<?php

declare(strict_types=1);

namespace Payum\Checkoutcom;

use Payum\Checkoutcom\Action\Api\ObtainSnippetAction;
use Payum\Checkoutcom\Action\AuthorizeAction;
use Payum\Checkoutcom\Action\CancelAction;
use Payum\Checkoutcom\Action\CaptureAction;
use Payum\Checkoutcom\Action\RefundAction;
use Payum\Checkoutcom\Action\StatusAction;
use Payum\Checkoutcom\Action\SyncAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class CheckoutcomGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritdoc}
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
            'payum.action.status' => new StatusAction(),
            'payum.action.sync' => new SyncAction(),
            'payum.action.obtain_token' => new ObtainSnippetAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
                'environment' => Api::TEST,
            );
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = ['publishable_key'];
            $config['payum.required_options'] = ['secrety_key'];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);
                $checkoutcomConfig = [
                    'publishable_key' => $config['publishable_key'],
                    'secrety_key' => $config['secrety_key'],
                    'environment' => $config['environment'],
                ];

                $checkoutcomConfig['checkoutjs_path'] =
                    $checkoutcomConfig['environment'] === Api::PRODUCTION ?
                        'https://cdn.checkout.com/js/checkout.js' :
                        'https://cdn.checkout.com/sandbox/js/checkout.js'
                ;

                $checkoutcomConfig['framesjs_path'] = 'https://cdn.checkout.com/js/frames.js';

                $checkoutcomConfig['type'] = 'LIGHTBOX';
                if ($config['type'] === 'FRAME') {
                    $checkoutcomConfig['type'] = 'FRAME';
                }

                return new Api($checkoutcomConfig);
            };
        }
    }
}
