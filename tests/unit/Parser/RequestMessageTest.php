<?php
declare(strict_types=1);

namespace PTS\Test\ParserPsr7\unit\Parser;

use PHPUnit\Framework\TestCase;
use PTS\ParserPsr7\Message\RequestMessage;

class RequestMessageTest extends TestCase
{
    /**
     * @param string $message
     * @param array $expected
     *
     * @dataProvider requestDataProvider
     */
    public function testParseRequest(string $message, array $expected): void
    {
        $message = new RequestMessage($message);
        [$method, $uri, $v, $headers, $body] = array_values($expected);

        self::assertSame($method, $message->getMethod());
        self::assertSame($uri, $message->getUri());
        self::assertSame($v, $message->getProtocolVersion());
        self::assertSame($headers, $message->getHeaders());
        self::assertSame($body, $message->getBody());
    }

    public function requestDataProvider(): array
    {
        return [
            [
                'message' => "GET / HTTP 1.0\r\nconnection:keep-alive\r\nh1: v1 \r\nuser: 1\r\n\r\n",
                [
                    'method' => 'GET',
                    'uri' => '/',
                    'v' => '1.0',
                    'headers' => [
                        'connection' => ['keep-alive'],
                        'h1' => ['v1'],
                        'user' => ['1']
                    ],
                    'body' => ''
                ]
            ],
            [
                'message' => "POST /create?new=1 HTTP 1.1\r\ncontent-type: application/json\r\n\r\n{\"name\":\"alex\"}",
                [
                    'method' => 'POST',
                    'uri' => '/create?new=1',
                    'v' => '1.1',
                    'headers' => [
                        'content-type' => ['application/json'],
                    ],
                    'body' => '{"name":"alex"}'
                ]
            ]
        ];
    }


    public function testGetProtocolVersion(): void
    {
        $message = "GET / HTTP 1.1\r\n\r\n";
        $message = new RequestMessage($message);

        self::assertSame('1.1', $message->getProtocolVersion());
        // check cache path
        self::assertSame('1.1', $message->getProtocolVersion());
    }

    public function testGetUri(): void
    {
        $message = "GET /send HTTP 1.1\r\n\r\n";
        $message = new RequestMessage($message);

        self::assertSame('/send', $message->getUri());
        // check cache path
        self::assertSame('/send', $message->getUri());
    }

    public function testSetHeaders(): void
    {
        $message = "GET /send HTTP 1.1\r\n\r\n";
        $message = new RequestMessage($message);
        $message->setHeaders([
            'connection' => ['keep-alive']
        ]);

        self::assertSame([
            'connection' => ['keep-alive']
        ], $message->getHeaders());
    }
}