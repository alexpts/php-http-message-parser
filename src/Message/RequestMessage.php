<?php
declare(strict_types=1);

namespace PTS\ParserPsr7\Message;

class RequestMessage extends Message
{

    protected string $uri = '';
    protected string $method = '';

    protected function parseStartLine(string $startLine): void
    {
        [$this->method, $uri, $_, $this->protocolVersion] = explode(' ', $startLine);
        $this->uri = $uri ?? '';
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
