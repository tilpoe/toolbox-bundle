<?php

namespace Feierstoff\ToolboxBundle\EventListener;

use Feierstoff\ToolboxBundle\Exception\BadRequestException;
use Feierstoff\ToolboxBundle\Exception\ForbiddenException;
use Feierstoff\ToolboxBundle\Exception\InternalServerException;
use Feierstoff\ToolboxBundle\Exception\NotFoundException;
use Feierstoff\ToolboxBundle\Exception\UnauthorizedException;
use Feierstoff\ToolboxBundle\Exception\ViolationException;
use Feierstoff\ToolboxBundle\Response\ExceptionResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener {

    public function __construct(
        private string $env,
        private readonly LoggerInterface $apiLogger
    ) {}

    public function __invoke(ExceptionEvent $event): void {
        $exception = $event->getThrowable();

        switch (true) {
            case $exception instanceof NotFoundException:
                $event->setResponse(new ExceptionResponse(null, ExceptionResponse::NOT_FOUND));
                return;

            case $exception instanceof ForbiddenException:
                $this->apiLogger->error("ForbiddenException");
                $event->setResponse(new ExceptionResponse(null, ExceptionResponse::FORBIDDEN));
                return;

            case $exception instanceof ViolationException:
                $event->setResponse(new ExceptionResponse($exception->getResponse(), ExceptionResponse::VIOLATION));
                return;

            case $exception instanceof UnauthorizedException:
                $this->apiLogger->error("UnauthorizedException");
                $event->setResponse(new ExceptionResponse(null, ExceptionResponse::UNAUTHORIZED));
                return;

            case $exception instanceof BadRequestException:
                $this->apiLogger->error("BadRequestException: {$exception->getResponse()}");
                $event->setResponse(new ExceptionResponse($exception->getResponse(), ExceptionResponse::BAD_REQUEST));
                return;

            default:
                $this->apiLogger->critical("InternalServerException: {$exception->getMessage()}, Trace: {$exception->getTraceAsString()}");

                $data = [
                    "message" => $exception->getMessage(),
                    "trace" => $exception->getTrace()
                ];

                if ($exception instanceof InternalServerException) {
                    $data["data"] = $exception->data;
                }

                if ($this->env != "prod") {
                    $event->setResponse(new ExceptionResponse($data));
                } else {
                    $event->setResponse(new ExceptionResponse());
                }
        }
    }

}