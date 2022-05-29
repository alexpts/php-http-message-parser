<?php

namespace PTS\Test\ParserPsr7\unit;

use PHPUnit\Framework\TestCase;
use PTS\ParserPsr7\Factory\Psr7Factory;
use PTS\ParserPsr7\SapiEmitter;

class SapiEmitterTest extends TestCase
{
    public function testEmit() {
        $emitter = new SapiEmitter;

        $psr7Factory = new Psr7Factory;

        $message = "HTTP/1.1 200 OK\r\ncontent-type:application/json\r\n\r\n{\"name\":\"alex\"}";
        $psr7Response = $psr7Factory->toPsr7Response($message);

        ob_start();
        $emitter->emit($psr7Response);
        $stdout = ob_get_clean();

        $this->assertSame($message, $stdout);
    }
}