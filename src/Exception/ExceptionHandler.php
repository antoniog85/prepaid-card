<?php declare(strict_types=1);

namespace PrepaidCard\Exception;

use Exception;
use PrepaidCard\Card\NotEnoughFundsException;
use PrepaidCard\HttpStatusCodes;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class ExceptionHandler
{
    private const DEFAULT_HTTP_STATUS_CODE = HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR;
    private const DEFAULT_LOG_LEVEL        = LogLevel::ERROR;

    /** @var Exception */
    private $exceptionThrown;

    /** @var LoggerInterface */
    private $logger;

    private $dontReport = [];

    /**
     * Map exceptions class => http status code
     *
     * @var array
     */
    private $httpExceptionsCodesMap = [
        EntityNotFoundException::class     => HttpStatusCodes::HTTP_NOT_FOUND,
        NotEnoughFundsException::class     => HttpStatusCodes::HTTP_BAD_REQUEST,
        \InvalidArgumentException::class   => HttpStatusCodes::HTTP_BAD_REQUEST,
        OperationForbiddenException::class => HttpStatusCodes::HTTP_BAD_REQUEST,
    ];

    /**
     * Map exceptions class => log level
     *
     * @var array
     */
    private $exceptionLogLevelsMap = [
        EntityNotFoundException::class     => LogLevel::DEBUG,
        NotEnoughFundsException::class     => LogLevel::DEBUG,
        \InvalidArgumentException::class   => LogLevel::DEBUG,
        OperationForbiddenException::class => LogLevel::NOTICE,
    ];

    public function __construct(Exception $exception, LoggerInterface $logger)
    {
        $this->exceptionThrown = $exception;
        $this->logger = $logger;
    }

    public function render(): array
    {
        $code = $this->exceptionThrown->getCode();
        $message = $this->exceptionThrown->getMessage();

        return [
            'code'    => $code,
            'message' => $message,
        ];
    }

    public function getHttpStatusCode(): int
    {
        foreach ($this->httpExceptionsCodesMap as $exception => $httpStatusCode) {
            if ($this->exceptionThrown instanceof $exception) {
                return $httpStatusCode;
            }
        }

        return self::DEFAULT_HTTP_STATUS_CODE;
    }

    public function report(): void
    {
        if ($this->shouldNotReport()) {
            return;
        }

        foreach ($this->exceptionLogLevelsMap as $exception => $logLevel) {
            if ($this->exceptionThrown instanceof $exception) {
                $this->logger->{$logLevel}($this->exceptionThrown->getMessage());

                return;
            }
        }

        $this->logger->{self::DEFAULT_LOG_LEVEL}($this->exceptionThrown);
    }

    private function shouldNotReport(): bool
    {
        foreach ($this->dontReport as $type) {
            if ($this->exceptionThrown instanceof $type) {
                return true;
            }
        }

        return false;
    }
}
