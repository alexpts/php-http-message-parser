<?php
declare(strict_types=1);

namespace PTS\ParserPsr7\Message;

class ResponseMessage extends Message
{

    protected ?int $statusCode = null;
    protected ?string $reasonPhrase = null;

    protected function parseProtocolVersion(string $startLine): string
    {
        $this->parseResponseStartLine($startLine);
        return $this->protocolVersion;
    }

    protected function parseResponseStartLine(string $startLine): void
    {
        [$protocolVersion, $statusCode, $this->reasonPhrase] = explode(' ', $startLine, 3);
        $this->statusCode = (int)$statusCode;
        $this->protocolVersion = substr($protocolVersion, 5);
    }

    public function getStatusCode(): int
    {
        if ($this->statusCode === null) {
            $this->parseResponseStartLine($this->startLine);
        }

        return $this->statusCode;
    }

    public function getReasonPhrase(): string
    {
        if ($this->reasonPhrase === null) {
            $this->parseResponseStartLine($this->startLine);
        }

        return $this->reasonPhrase ?? '';
    }
}