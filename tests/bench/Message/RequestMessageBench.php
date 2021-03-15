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
use PTS\ParserPsr7\Message\RequestMessage;

class RequestMessageBench
{

    protected RequestMessage $message;


    /**
     * @Revs(10)
     * @Iterations(4)
     * @ParamProviders({"requestProvider"})
     * @OutputTimeUnit("microseconds", precision=3)
     * @Warmup(1)
     *
     * @Assert("mode(variant.time.avg) < 1 microseconds")
     *
     * @param array $params
     */
    public function benchRequestMessage(array $params): void
    {
        [$httpMessage] = $params;
        new RequestMessage($httpMessage);
    }

    /**
     * @Revs(10)
     * @Iterations(4)
     * @ParamProviders({"requestProvider"})
     * @OutputTimeUnit("microseconds", precision=3)
     * @ OutputMode("throughput") - выводить число операций в единицу времени, а не среднее время
     * @Warmup(1)
     *
     * @Assert("mode(variant.time.avg) < 4 microseconds")
     */
    public function benchRequestMessageParseHeaders(array $params): void
    {
        [$httpMessage] = $params;
        $message = new RequestMessage($httpMessage);
        $message->getHeaders();
    }

    public function requestProvider(): array
    {
        return [
            'GET' => ["GET / HTTP 1.1\r\nConnection: Keep-Alive\r\nh1: v1 \r\nuser: 1\r\n\r\n"],
            'POST' => ["POST /my/account HTTP 1.1\r\nConnection: Keep-Alive\r\nToken: ds1223k#fk5423lSDL\r\n\r\n"],
            'Many Headers' => ["GET /my/account HTTP 1.1\r\nConnection: Keep-Alive\r\nDate: Sun, 18 Oct 2012 10:36:20 GMT\r\nServer: Apache/2.2.14 (Win32)\r\nContent-Length: 0\r\nContent-Type: text/html; charset=iso-8859-1\r\nDNT:1\r\nX-Web: some1\r\nToken: ds1223k#fk5423lSDL\r\n\r\n"],
        ];
    }
}