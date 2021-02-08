<?php
declare(strict_types=1);

namespace PTS\Test\ParserPsr7\unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PTS\ParserPsr7\Message\HeadersValidator;
use PTS\ParserPsr7\Message\RequestMessage;

class HeaderValidatorTest extends TestCase
{

    public function testBadHeaderName(): void
    {
        $message = "GET / HTTP 1.1\r\nHeader#$: value\r\n\r\n";
        $message = new RequestMessage($message);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Header names is incorrect: Header#$');

        $validator = new HeadersValidator;
        $validator->validate($message->getHeaders());
    }

    public function testBadHeaderValue(): void
    {
        $message = "GET / HTTP 1.1\r\nHeader: value\x19\r\n\r\n";
        $message = new RequestMessage($message);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value is incorrect for one of the header.');

        $validator = new HeadersValidator;
        $validator->validate($message->getHeaders());
    }

    public function testEmptyHeaders(): void
    {
        $message = "GET / HTTP 1.1\r\n\r\n";
        $message = new RequestMessage($message);

        $validator = new HeadersValidator;
        $validator->validate($message->getHeaders());

        self::assertSame([], $message->getHeaders());
    }
}