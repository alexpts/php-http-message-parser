<?php
declare(strict_types=1);

namespace PTS\ParserPsr7\Message;

use function explode;
use function trim;

abstract class Message
{
    public const BODY_DELIMITER = "\r\n\r\n";

    protected ?string $startLine = null;
    protected array $rawHeaders = [];
    protected ?string $body = null;

    protected ?string $protocolVersion = null;
    protected ?int $statusCode = null;
    protected ?string $reasonPhrase = null;
    protected ?array $headers = null;


    public function __construct(string $message)
    {
        [$head, $this->body] = explode(self::BODY_DELIMITER, $message);
        $this->rawHeaders = explode("\r\n", $head);
        $this->startLine = array_shift($this->rawHeaders);
    }

    abstract protected function parseProtocolVersion(string $startLine): string;

    public function getProtocolVersion(): string
    {
        if ($this->protocolVersion === null) {
            $this->protocolVersion = $this->parseProtocolVersion($this->startLine);
        }

        return $this->protocolVersion;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function getHeaders(): array
    {
        if ($this->headers === null) {
            $this->headers = $this->parseHeaders($this->rawHeaders);
        }

        return $this->headers;
    }

    protected function parseHeaders(array $rawHeaders): array
    {
        $headers = [];

        foreach ($rawHeaders as $line) {
            $parts = explode(':', $line, 2);
            $headers[trim($parts[0])][] = isset($parts[1]) ? trim($parts[1]) : null;
        }

        return $headers;
    }
}