<?php
declare(strict_types=1);

namespace PTS\ParserPsr7\Message;

use function explode;
use function preg_match_all;
use function trim;

abstract class Message
{
    protected static array $cache = [];

    public const BODY_DELIMITER = "\r\n\r\n";
    public const HEADER_DELIMITER = "\r\n";

    protected string $startLine = '';
    protected string $rawHeaders = '';
    protected ?string $body = null;

    protected ?string $protocolVersion = null;
    protected ?int $statusCode = null;
    protected ?string $reasonPhrase = null;
    protected ?array $headers = null;


    public function __construct(string $message)
    {
        [$this->rawHeaders, $this->body] = explode(self::BODY_DELIMITER, $message, 2);

        $p = explode(self::HEADER_DELIMITER, $this->rawHeaders, 2);
        $this->startLine = $p[0];
        $this->rawHeaders = $p[1] ?? '';
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
            $this->headers = $this->rawHeaders ? $this->parseHeaders($this->rawHeaders) : [];
            //$this->headers = $this->rawHeaders ? $this->parseHeaderRegExp($this->rawHeaders) : [];
        }

        return $this->headers;
    }

    // сравнить этот кейс с отдельной валидацией, возможно захват с валидацией будет быстрее, чем отдельно валидировать
    protected function parseHeaderRegExp(string $rawHeaders): array
    {
        $headers = [];
        $matches = [];
        preg_match_all('/^([^()<>@,;:\\\"\/\[\]?={}\x01-\x20\x7F]++):[\x20\x09]*+((?:[\x20\x09]*+[\x21-\x7E\x80-\xFF]++)*+)[\x20\x09]*+[\r\n]?+/m', $rawHeaders, $matches, \PREG_SET_ORDER);

        foreach ($matches as $m) {
            $headers[$m[1]][] = $m[2];
        }

        return $headers;
    }

    protected function parseHeaders(string $rawHeaders): array
    {
        $headers = [];

        if (static::$cache[$rawHeaders] ?? false) {
            return static::$cache[$rawHeaders];
        }

        $lines = explode(self::HEADER_DELIMITER, $this->rawHeaders);
        foreach ($lines as $line) {
            $parts = explode(':', $line, 2);
            $name = trim($parts[0]);
            $value = $parts[1] ?? '';
            $headers[$name][] = trim($value);
        }

        static::$cache[$rawHeaders] = $headers;
        return $headers;
    }
}