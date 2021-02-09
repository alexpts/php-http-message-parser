<?php
declare(strict_types=1);

namespace PTS\ParserPsr7\Message;


use function explode;
use function trim;

abstract class Message
{
    protected static array $cache = [];

    protected const BODY_DELIMITER = "\r\n\r\n";
    protected const HEADER_DELIMITER = "\r\n";

    protected string $startLine = '';
    protected string $rawHeaders = '';
    protected string $body = '';
    protected string $protocolVersion = '';

    protected ?array $headers = null;


    public function __construct(string $message)
    {
        [$this->rawHeaders, $this->body] = explode(self::BODY_DELIMITER, $message, 2);

        $p = explode(self::HEADER_DELIMITER, $this->rawHeaders, 2);
        $this->parseStartLine($p[0]);
        $this->rawHeaders = $p[1] ?? '';
    }

    abstract protected function parseStartLine(string $startLine): void;

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getHeaders(): array
    {
        if ($this->headers === null) {
            $this->headers = $this->rawHeaders ? $this->parseHeaders($this->rawHeaders) : [];
            $this->rawHeaders = '';
        }

        return $this->headers ?? [];
    }

    public function setHeaders(array $headers): static
    {
        $this->headers = $headers;
        return $this;
    }

    protected function parseHeaders(string $rawHeaders): array
    {
        $headers = [];

        #if (static::$cache[$rawHeaders] ?? false) {
        #    return static::$cache[$rawHeaders];
        #}

        $lines = explode(self::HEADER_DELIMITER, $rawHeaders);
        foreach ($lines as $line) {
            $pair = explode(':', $line, 2);
            $name = $pair[0];
            $value = $pair[1] ?? '';

            $headers[$name][] = trim($value);
        }

        #static::$cache[$rawHeaders] = $headers;
        return $headers;
    }
}