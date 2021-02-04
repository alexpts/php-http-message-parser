<?php

namespace PTS\ParserPsr7\Factory;

use Psr\Http\Message\ServerRequestInterface;
use PTS\ParserPsr7\Message\RequestMessage;

class RequestPsr7Factory implements RequestPsr7FactoryInterface
{

    public function __construct($psr7Factory)
    {

    }

    public function toMessage(ServerRequestInterface $request): string
    {
        // TODO: Implement toMessage() method.
    }

    public function toPsr7(string $request): ServerRequestInterface
    {
        $message = new RequestMessage($request);
        // ленивые заголовки
    }
}