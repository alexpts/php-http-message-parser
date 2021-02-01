<?php
declare(strict_types=1);

namespace PTS\ParserPsr7\Message;

class RequestMessage extends Message
{

    protected ?string $uri = null;
    protected ?string $method = null;


    protected function parseProtocolVersion(string $startLine): string
    {
        $this->parseRequestStartLine($startLine);
        return $this->protocolVersion;
    }

    protected function parseRequestStartLine(string $startLine): void
    {
        [$this->method, $uri, $_, $this->protocolVersion] = explode(' ', $startLine);
        $this->uri = $uri ?? '';
    }

    public function getUri(): string
    {
        if ($this->uri === null) {
            $this->parseRequestStartLine($this->startLine);
        }

        return $this->uri ?? '';
    }

    public function getMethod(): string
    {
        if ($this->method === null) {
            $this->parseRequestStartLine($this->startLine);
        }

        return $this->method ?? '';
    }
}