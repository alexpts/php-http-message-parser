<?php

namespace PTS\ParserPsr7\Factory;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PTS\ParserPsr7\Message\HeadersValidator;
use PTS\ParserPsr7\Message\RequestMessage;
use PTS\ParserPsr7\Message\ResponseMessage;
use PTS\Psr7\Response;
use PTS\Psr7\ServerRequest;
use PTS\Psr7\Uri;

class Psr7Factory implements Psr7FactoryInterface
{
    protected ?HeadersValidator $headerValidator = null;

    public function __construct()
    {
        $this->headerValidator = new HeadersValidator;
    }

    public function toMessageRequest(ServerRequestInterface $request): string
    {
        $message = sprintf(
            "%s %s HTTP/%s\r\n",
            $request->getMethod(),
            $request->getUri(),
            $request->getProtocolVersion()
        );
        $message .= $this->getHeaders($request) . "\r\n\r\n";
        return $message . $request->getBody();
    }

    public function toMessageResponse(ResponseInterface $response): string
    {
        $message = sprintf(
            "HTTP/%s %d %s\r\n",
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );
        $message .= $this->getHeaders($response) . "\r\n\r\n";
        return $message . $response->getBody();
    }

    protected function getHeaders(MessageInterface $message): string
    {
        $headers = '';

        foreach ($message->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $headers .= "$name:$value\r\n";
            }
        }

        return rtrim($headers, "\r\n");
    }

    public function toPsr7Request(string $httpMessage): ServerRequestInterface
    {
        $message = new RequestMessage($httpMessage);
        $headers = $message->getHeaders();

        if ($this->headerValidator) {
            $this->headerValidator->validate($headers);
        }

        // @todo lazy headers
        return new ServerRequest(
            $message->getMethod(),
            new Uri($message->getUri()),
            $headers,
            $message->getBody(),
            $message->getProtocolVersion()
        );
    }

    public function toPsr7Response(string $httpMessage): ResponseInterface
    {
        $response = new ResponseMessage($httpMessage);

        return new Response(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody(),
            $response->getProtocolVersion(),
        );
    }
}