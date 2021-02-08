<?php
declare(strict_types=1);

namespace PTS\Test\ParserPsr7\unit\Factory;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use PTS\ParserPsr7\Factory\Psr7Factory;

class Psr7FactoryTest extends TestCase
{

    /**
     * @param string $message
     * @param array $expected
     *
     * @dataProvider requestDataProvider
     */
    public function testCreatePsr7Request(string $message, array $expected): void
    {
        [$method, $uri, $v, $headers, $body] = array_values($expected);

        $factory = new Psr7Factory;
        $request = $factory->toPsr7Request($message);

        $uriRequest = $request->getUri();

        self::assertInstanceOf(RequestInterface::class, $request);
        self::assertInstanceOf(UriInterface::class, $uriRequest);
        self::assertSame($uri, (string)$uriRequest);
        self::assertSame($v, $request->getProtocolVersion());
        self::assertSame($headers, $request->getHeaders());
        self::assertSame($body, (string)$request->getBody());
        self::assertSame($method, $request->getMethod());
    }

    /**
     * @param string $message
     *
     * @dataProvider requestDataProvider2
     */
    public function testToMessagePsr7Request(string $message, string $expected): void
    {
        $factory = new Psr7Factory;
        $request = $factory->toPsr7Request($message);

        $result = $factory->toMessageRequest($request);
        self::assertSame($expected, $result);
    }

    public function requestDataProvider2(): array
    {
        return [
            [
                'message' => "GET / HTTP 1.0\r\nconnection:keep-alive\r\nh1: v1 \r\nuser: 1\r\n\r\n",
                'expected' => "GET / HTTP 1.0\r\nconnection:keep-alive\r\nh1:v1\r\nuser:1\r\n\r\n",
            ],
            [
                'message' => "POST /create?new=1 HTTP 1.1\r\ncontent-type: application/json\r\n\r\n{\"name\":\"alex\"}",
                'expected' => "POST /create?new=1 HTTP 1.1\r\ncontent-type:application/json\r\n\r\n{\"name\":\"alex\"}",
            ]
        ];
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

    /**
     * @param string $message
     * @param array $expected
     *
     * @dataProvider responseDataProvider
     */
    public function testCreatePsr7Response(string $message, array $expected): void
    {
        [$code, $reason, $v, $headers, $body] = array_values($expected);

        $factory = new Psr7Factory;
        $response = $factory->toPsr7Response($message);

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertSame($code, $response->getStatusCode());
        self::assertSame($v, $response->getProtocolVersion());
        self::assertSame($headers, $response->getHeaders());
        self::assertSame($body, (string)$response->getBody());
        self::assertSame($reason, $response->getReasonPhrase());
    }

    public function responseDataProvider(): array
    {
        return [
            [
                'message' => "HTTP/1.1 404 Not Found\r\nDate: Sun, 18 Oct 2012 10:36:20 GMT\r\nServer: Apache/2.2.14 (Win32)\r\nContent-Length: 230\r\nConnection: Closed\r\nContent-Type: application/json; charset=utf-8\r\n\r\n{\"name\":\"alex\"}",
                [
                    'code' => 404,
                    'reason' => 'Not Found',
                    'v' => '1.1',
                    'headers' => [
                        'Date' => ['Sun, 18 Oct 2012 10:36:20 GMT'],
                        'Server' => ['Apache/2.2.14 (Win32)'],
                        'Content-Length' => ['230'],
                        'Connection' => ['Closed'],
                        'Content-Type' => ['application/json; charset=utf-8'],
                    ],
                    'body' => '{"name":"alex"}',
                ],
            ],
            [
                'message' => "HTTP/1.1 201 Created\r\n\r\n",
                [
                    'code' => 201,
                    'reason' => 'Created',
                    'v' => '1.1',
                    'headers' => [],
                    'body' => '',
                ],
            ],
        ];
    }

    /**
     * @param string $message
     * @param string $expected
     *
     * @dataProvider responseDataProvider2
     */
    public function testToMessagePsr7Response(string $message, string $expected): void
    {
        $factory = new Psr7Factory;
        $response = $factory->toPsr7Response($message);

        $result = $factory->toMessageResponse($response);
        self::assertSame($expected, $result);
    }

    public function responseDataProvider2(): array
    {
        return [
            [
                'message' => "HTTP/1.1 201 Created\r\nconnection:keep-alive\r\nh1: v1 \r\nuser: 1\r\n\r\n",
                'expected' => "HTTP/1.1 201 Created\r\nconnection:keep-alive\r\nh1:v1\r\nuser:1\r\n\r\n",
            ],
            [
                'message' => "HTTP/1.1 200 OK\r\ncontent-type: application/json\r\n\r\n{\"name\":\"alex\"}",
                'expected' => "HTTP/1.1 200 OK\r\ncontent-type:application/json\r\n\r\n{\"name\":\"alex\"}",
            ]
        ];
    }

}