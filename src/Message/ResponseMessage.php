<?php
declare(strict_types=1);

namespace PTS\ParserPsr7\Message;

class ResponseMessage extends Message
{

    protected int $statusCode = 0;
    protected string $reasonPhrase = '';

    protected function parseStartLine(string $startLine): void
    {
        [$protocolVersion, $statusCode, $this->reasonPhrase] = explode(' ', $startLine, 3);
        $this->statusCode = (int)$statusCode;
        $this->protocolVersion = substr($protocolVersion, 5);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }
}