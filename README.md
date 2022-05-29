# php-http-message-parser

[![phpunit](https://github.com/alexpts/php-http-message-parser/actions/workflows/phpunit.yml/badge.svg?branch=main)](https://github.com/alexpts/php-http-message-parser/actions/workflows/phpunit.yml)
[![codecov](https://codecov.io/gh/alexpts/php-http-message-parser/branch/main/graph/badge.svg?token=14L6IJA5UE)](https://codecov.io/gh/alexpts/php-http-message-parser)

```bash
composer require alexpts/http-message-parser
```

Very fast parser for http message.

- Create PSR-7 request/response from http message
- Create http message from PSR-7 request/response
- SapiEmitter

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

`composer bench`
