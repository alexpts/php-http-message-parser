<?php
declare(strict_types=1);

namespace PTS\ParserPsr7\Message;

use InvalidArgumentException;
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
    protected string $body = '';
    protected string $protocolVersion = '';

    protected ?array $headers = null;


    public function __construct(string $message)
    {
        [$this->rawHeaders, $this->body] = explode(self::BODY_DELIMITER, $message, 2);

        $p = explode(self::HEADER_DELIMITER, $this->rawHeaders, 2);
        //$this->parseStartLine($p[0]);
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

    public function getHeaders(bool $validate = true): array
    {
        if ($this->headers === null) {
            $this->headers = $this->rawHeaders ? $this->parseHeaders($this->rawHeaders, $validate) : [];
            //$this->headers = $this->rawHeaders ? $this->parseHeaderRegExp($this->rawHeaders) : [];
            $this->rawHeaders = '';
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

    protected function parseHeaders(string $rawHeaders, bool $validate = true): array
    {
        $headers = [];

        #if (static::$cache[$rawHeaders] ?? false) {
        #    return static::$cache[$rawHeaders];
        #}

        $lines = explode(self::HEADER_DELIMITER, $this->rawHeaders);
        foreach ($lines as $line) {
            $pair = explode(':', $line, 2);
            $name = $pair[0];
            $value = $pair[1] ?? '';

            $headers[$name][] = trim($value);
        }

        $validate && $this->validateHeaders($headers);
        #static::$cache[$rawHeaders] = $headers;
        return $headers;
    }

    /**
     * @param array $headers
     *
     * It is more strict validate than RFC-7230
     */
    protected function validateHeaders(array $headers): void
    {
        $names = implode('', array_keys($headers));
        if (preg_match("/^[~0-9A-Za-z-+_.]+$/", $names) !== 1) {
            throw new InvalidArgumentException('Header names is incorrect.');
        }

        $allValues = '';
        foreach ($headers as $values) {
            foreach ($values as $value) {
                $allValues .= $value;
            }
        }

        if (preg_match("/^[\x20-\x7E\x80-\xFF]+$/", $allValues) !== 1) {
            throw new InvalidArgumentException('The value is incorrect for one of the header.');
        }
    }
}