<?php

namespace PTS\Test\ParserPsr7\unit;

use PHPUnit\Framework\TestCase;
use PTS\ParserPsr7\Factory\Psr7Factory;
use PTS\ParserPsr7\SapiEmitter;

class SapiEmitterTest extends TestCase
{
    /**
     * @return void
     * @runInSeparateProcess
     */
    public function testEmit(): void
    {
        if (!function_exists('xdebug_get_headers')) {
            $this->markTestSkipped('test use xdebug extension');
        }

        $emitter = new SapiEmitter;
        $psr7Factory = new Psr7Factory;

        $message = "HTTP/1.1 202 OK\r\ncontent-type:application/json\r\n\r\n{\"name\":\"alex\"}";
        $psr7Response = $psr7Factory->toPsr7Response($message);

        ob_start();
        $emitter->emit($psr7Response);
        $headers = xdebug_get_headers();
        $stdout = ob_get_clean();
        $statusCode = http_response_code();

        $this->assertSame($headers, [
            'content-type: application/json'
        ]);
        $this->assertSame("{\"name\":\"alex\"}", $stdout);
        $this->assertSame(202, $statusCode);
    }
}