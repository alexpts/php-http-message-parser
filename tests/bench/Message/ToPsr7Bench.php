<?php

namespace PTS\Test\ParserPsr7\bench\Message;

use PhpBench\Benchmark\Metadata\Annotations\Assert;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\OutputMode;
use PhpBench\Benchmark\Metadata\Annotations\OutputTimeUnit;
use PhpBench\Benchmark\Metadata\Annotations\ParamProviders;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Skip;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;
use PTS\ParserPsr7\Factory\Psr7Factory;
use PTS\Psr7\Response\JsonResponse;

class ToPsr7Bench
{

    protected Psr7Factory $factory;

    public function __construct()
    {
        $this->factory = new Psr7Factory;
    }

    /**
     * @Revs(10)
     * @Iterations(4)
     * @ParamProviders({"requestProvider"})
     * @OutputTimeUnit("microseconds", precision=3)
     * @Warmup(1)
     * @Assert("variant.mode < 20 microseconds")
     * @param array $params
     */
    public function benchCreatePsr7Request(array $params): void
    {
        [$httpMessage] = $params;
        $this->factory->toPsr7Request($httpMessage);
    }

    public function requestProvider(): array
    {
        return [
            'GET' => ["GET / HTTP 1.1\r\nConnection: Keep-Alive\r\nh1: v1 \r\nuser: 1\r\n\r\n"],
            'POST' => ["POST /my/account HTTP 1.1\r\nConnection: Keep-Alive\r\nToken: ds1223k#fk5423lSDL\r\n\r\n"],
            'Many Headers' => ["GET /my/account HTTP 1.1\r\nConnection: Keep-Alive\r\nDate: Sun, 18 Oct 2012 10:36:20 GMT\r\nServer: Apache/2.2.14 (Win32)\r\nContent-Length: 0\r\nContent-Type: text/html; charset=iso-8859-1\r\nDNT:1\r\nX-Web: some1\r\nToken: ds1223k#fk5423lSDL\r\n\r\n"],
            'Some' => ["GET / HTTP 1.1\r\nHost: some.dev\r\nUser-Agent: Mozilla/5.0 (Windows NT 6.1; rv:18.0) Gecko/20100101 Firefox/18.0\r\nAccept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\nAccept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3\r\nAccept-Encoding: gzip, deflate\r\nCookie: wp-settings\r\nConnection: keep-alive\r\n\r\n"],
        ];
    }

    /**
     * @Revs(10)
     * @Iterations(4)
     * @ParamProviders({"responseProvider"})
     * @OutputTimeUnit("microseconds", precision=3)
     * @Warmup(1)
     * @Assert("variant.mode < 20 microseconds")
     * @param array $params
     */
    public function benchCreatePsr7Response(array $params): void
    {
        [$httpMessage] = $params;
        $this->factory->toPsr7Response($httpMessage);
    }

    public function responseProvider(): array
    {
        return [
            ["HTTP/1.1 200 OK\r\nConnection: Keep-Alive\r\nh1: v1 \r\nuser: 1\r\n\r\n"],
            ["HTTP/1.1 200 OK\r\nConnection: Keep-Alive\r\nDate: Sun, 18 Oct 2012 10:36:20 GMT\r\nServer: Apache/2.2.14 (Win32)\r\nContent-Length: 0\r\nContent-Type: text/html; charset=iso-8859-1\r\nDNT:1\r\nX-Web: some1\r\nToken: ds1223k#fk5423lSDL\r\n\r\n"],
            ["HTTP/1.1 200 OK\r\nHost: some.dev\r\nUser-Agent: Mozilla/5.0 (Windows NT 6.1; rv:18.0) Gecko/20100101 Firefox/18.0\r\nAccept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\nAccept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3\r\nAccept-Encoding: gzip, deflate\r\nCookie: wp-settings\r\nConnection: keep-alive\r\n\r\n"],
        ];
    }

    /**
     * @Revs(10)
     * @Iterations(4)
     * @OutputTimeUnit("microseconds", precision=3)
     * @Warmup(1)
     * @Assert("variant.mode < 8 microseconds")
     */
    public function benchEmitterPsr7Response(): void
    {
        $response = new JsonResponse(['status' => 'ok'], 200, [
            'connection' => 'Keep-Alive',
            'content-length' => '10',
        ]);

        $this->factory->toMessageResponse($response);
    }

}