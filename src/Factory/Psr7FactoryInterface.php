<?php

namespace PTS\ParserPsr7\Factory;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface Psr7FactoryInterface
{
    public function toMessageRequest(ServerRequestInterface $request): string;

    public function toPsr7Request(string $httpMessage): RequestInterface;

    public function toMessageResponse(ResponseInterface $response): string;

    public function toPsr7Response(string $httpMessage): ResponseInterface;
}