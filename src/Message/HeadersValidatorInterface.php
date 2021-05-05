<?php
declare(strict_types=1);

namespace PTS\ParserPsr7\Message;

interface HeadersValidatorInterface
{

    public function validate(array $headers): void;
}