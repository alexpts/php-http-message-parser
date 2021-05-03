[![Build Status](https://travis-ci.com/alexpts/php-http-message-parser.svg?branch=main)](https://travis-ci.com/alexpts/php-http-message-parser.svg?branch=main)
[![Code Coverage](https://scrutinizer-ci.com/g/alexpts/php-http-message-parser/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/alexpts/php-http-message-parser/?branch=main)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alexpts/php-http-message-parser/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/alexpts/php-http-message-parser/?branch=main)


# php-http-message-parser


Very fast parser for http message.

- Create PSR-7 request/response from http message
- Create http message from PSR-7 request/response

```php
<?php

use PTS\ParserPsr7\Factory\Psr7Factory;

include_once 'vendor/autoload.php';

$factory = new Psr7Factory;

$httpMessage = "GET / HTTP 1.0\r\nconnection:keep-alive\r\nh1: v1 \r\nuser: 1\r\n\r\n";
$psr7Request = $factory->toPsr7Request($httpMessage);
$httpMessage2 = $factory->toMessageRequest($psr7Request); // "GET / HTTP 1.0\r\nconnection:keep-alive\r\nh1:v1\r\nuser:1\r\n\r\n"

$httpMessage = "HTTP/1.1 404 Not Found\r\n\r\n";
$psr7Response = $factory->toPsr7Request($httpMessage);
$httpMessage2 = $factory->toMessageRequest($psr7Response); // "HTTP/1.1 404 Not Found\r\n\r\n"

```


### Benchmark Tests

`cd tests && php ../vendor/bin/phpbench run --report=aggregate`
`cd tests && php ../vendor/bin/phpbench run --report=aggregate --filter=benchCreatePsr7Response`
or
`composer bench`
