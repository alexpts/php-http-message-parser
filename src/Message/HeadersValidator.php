<?php

namespace PTS\ParserPsr7\Message;

use InvalidArgumentException;

class HeadersValidator implements HeadersValidatorInterface
{
    /**
     * @param array $headers
     *
     * It is more strict validate than RFC-7230
     * https://nginx.org/docs/http/ngx_http_core_module.html#ignore_invalid_headers
     */
    public function validate(array $headers): void
    {
        if (count($headers)) {
            $names = implode('', array_keys($headers));
            if (preg_match("/^[~0-9A-Za-z-+_.]+$/", $names) !== 1) {
                throw new InvalidArgumentException("Header names is incorrect: $names");
            }

            $this->validateValues($headers);
        }
    }

    protected function validateValues(array $headers): void
    {
        $allValues = '';
        foreach ($headers as $values) {
            foreach ($values as $value) {
                $allValues .= $value;
            }
        }

        if ($allValues && preg_match("/^[\x20-\x7E\x80-\xFF]+$/", $allValues) !== 1) {
            throw new InvalidArgumentException('The value is incorrect for one of the header.');
        }
    }
}