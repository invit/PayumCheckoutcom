<?php

namespace Payum\Checkoutcom\Action\Api;

use Payum\Checkoutcom\Reply\PublicReply;
use Payum\Checkoutcom\Request\Api\ObtainSnippet;
use Payum\Checkoutcom\Request\Api\ObtainToken;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\RenderTemplate;

class ObtainSnippetAction extends BaseApiAwareAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @param ObtainToken $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);
        $model = ArrayObject::ensureArrayObject($request->getModel());

        throw new PublicReply(
            $this->api->getOptions()['public_key'],
            $this->api->getOptions()['framesjs_path']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof ObtainSnippet &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
