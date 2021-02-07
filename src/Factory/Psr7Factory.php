<?php

namespace PTS\ParserPsr7\Factory;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PTS\ParserPsr7\Message\RequestMessage;
use PTS\ParserPsr7\Message\ResponseMessage;
use PTS\Psr7\Response;
use PTS\Psr7\ServerRequest;
use PTS\Psr7\Uri;

class Psr7Factory implements Psr7FactoryInterface
{

    public function toMessageRequest(ServerRequestInterface $request): string
    {
        $message = $this->getStartLineRequest($request) . "\r\n";
        $message .= $this->getHeaders($request) . "\r\n\r\n";
        return $message . $request->getBody();
    }

    public function toMessageResponse(ResponseInterface $response): string
    {
        $message = $this->getStartLineResponse($response) . "\r\n";
        $message .= $this->getHeaders($response) . "\r\n\r\n";
        return $message . $response->getBody();
    }

    protected function getStartLineResponse(ResponseInterface $response): string
    {
        return sprintf(
            'HTTP/%s %d %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );
    }

    protected function getStartLineRequest(RequestInterface $request): string
    {
        return sprintf(
            '%s %s HTTP %s',
            $request->getMethod(),
            $request->getUri(),
            $request->getProtocolVersion()
        );
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

        // @todo lazy headers
        $request = new ServerRequest(
            $message->getMethod(),
            new Uri($message->getUri()),
            $message->getHeaders(false),
            $message->getBody(),
            $message->getProtocolVersion()
        );


        return $request;
    }

    public function toPsr7Response(string $httpMessage): ResponseInterface
    {
        $request = new ResponseMessage($httpMessage);

        return new Response(
            $request->getStatusCode(),
            $request->getHeaders(),
            $request->getBody(),
            $request->getProtocolVersion(),
        );
    }
}