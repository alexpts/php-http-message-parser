<?php

use PHPUnit\Framework\TestCase;
use PTS\ParserPsr7\Message\ResponseMessage;

class ResponseMessageTest extends TestCase
{
    /**
     * @param string $message
     * @param array $expected
     *
     * @dataProvider requestDataProvider
     */
    public function testParseRequest(string $message, array $expected): void
    {
        $message = new ResponseMessage($message);
        [$statusCode, $reason, $v, $headers, $body] = array_values($expected);

        self::assertSame($statusCode, $message->getStatusCode());
        self::assertSame($reason, $message->getReasonPhrase());
        self::assertSame($v, $message->getProtocolVersion());
        self::assertSame($headers, $message->getHeaders());
        self::assertSame($body, $message->getBody());
    }

    public function requestDataProvider(): array
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

    public function testGetProtocolVersion(): void
    {
        $message = "HTTP/1.1 404 Not Found\r\n\r\n";
        $message = new ResponseMessage($message);

        self::assertSame('1.1', $message->getProtocolVersion());
        // check cache path
        self::assertSame('1.1', $message->getProtocolVersion());
    }

    public function testGetReasonPhrase(): void
    {
        $message = "HTTP/1.1 404 Not Found\r\n\r\n";
        $message = new ResponseMessage($message);

        self::assertSame('Not Found', $message->getReasonPhrase());
        // check cache path
        self::assertSame('Not Found', $message->getReasonPhrase());
    }

}