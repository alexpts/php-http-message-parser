<?php

namespace PTS\ParserPsr7\Factory;

use Psr\Http\Message\ServerRequestInterface;

interface RequestPsr7FactoryInterface
{
    public function toMessage(ServerRequestInterface $request): string;

    public function toPsr7(string $request): ServerRequestInterface;
}