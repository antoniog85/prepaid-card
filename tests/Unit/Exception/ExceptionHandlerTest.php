<?php

namespace Test\Unit\Exception;

use PHPUnit\Framework\MockObject\MockObject;
use PrepaidCard\Exception\EntityNotFoundException;
use PrepaidCard\Exception\ExceptionHandler;
use PrepaidCard\HttpStatusCodes;
use Test\Unit\UnitTestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class ExceptionHandlerTest extends UnitTestCase
{
    /** @var MockObject */
    private $logger;

    public function setUp()
    {
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testItShouldRenderTheException()
    {
        $exception = new EntityNotFoundException('test message', 658);
        $expected = [
            'code'    => 658,
            'message' => 'test message',
        ];

        $handler = new ExceptionHandler($exception, $this->logger);

        self::assertSame($expected, $handler->render());
    }

    public function exceptionHttpCodesProvider()
    {
        return [
            [EntityNotFoundException::class, HttpStatusCodes::HTTP_NOT_FOUND],
        ];
    }

    /**
     * @dataProvider exceptionHttpCodesProvider
     */
    public function testItShouldReturnTheCorrectHttpCode($exceptionClass, $httpCode)
    {
        $handler = new ExceptionHandler(new $exceptionClass(''), $this->logger);

        self::assertSame($httpCode, $handler->getHttpStatusCode());
    }

    public function testItShouldReport()
    {
        $this->logger
            ->expects(self::once())
            ->method('error');

        $handler = new ExceptionHandler(new \Exception(), $this->logger);
        $handler->report();
    }

    public function exceptionLogLevelsProvider()
    {
        return [
            [EntityNotFoundException::class, LogLevel::DEBUG],
            [\InvalidArgumentException::class, LogLevel::DEBUG],
        ];
    }

    /**
     * @dataProvider exceptionLogLevelsProvider
     */
    public function testItShouldLogWithTheCorrectLogLevel(string $exceptionClass, string $logLevel)
    {
        $this->logger
            ->expects(self::once())
            ->method($logLevel);

        $handler = new ExceptionHandler(new $exceptionClass(''), $this->logger);
        $handler->report();
    }
}
