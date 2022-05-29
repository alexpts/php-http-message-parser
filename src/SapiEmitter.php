<?php

namespace PTS\ParserPsr7;

use Psr\Http\Message\ResponseInterface;
use PTS\ParserPsr7\Factory\Psr7Factory;
use PTS\ParserPsr7\Factory\Psr7FactoryInterface;

class SapiEmitter
{
    protected Psr7FactoryInterface $factory;

    public function __construct(Psr7FactoryInterface $factory = null)
    {
        $this->factory = $factory ?? new Psr7Factory;
    }

    public function emit(ResponseInterface $psr7Response): void {
        echo $this->factory->toMessageResponse($psr7Response);
    }
}