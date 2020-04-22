<?php

declare(strict_types=1);

namespace Payum\Checkoutcom\Reply;

class PublicReply extends \Exception implements \Payum\Core\Reply\ReplyInterface, \JsonSerializable
{
    private string $publicKey;
    private string $scriptPath;

    public function __construct(string $publicKey, string $scriptPath)
    {
        $this->publicKey = $publicKey;
        $this->scriptPath = $scriptPath;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getScriptPath(): string
    {
        return $this->scriptPath;
    }

    public function jsonSerialize() : array
    {
        return [
            'publicKey' => $this->publicKey,
            'scriptPath' => $this->scriptPath,
        ];
    }
}
