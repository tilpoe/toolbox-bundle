<?php

namespace Feierstoff\ToolboxBundle\EventListener;

use Feierstoff\ToolboxBundle\Exception\BadRequestException;
use Feierstoff\ToolboxBundle\Exception\ForbiddenException;
use Feierstoff\ToolboxBundle\Exception\InternalServerException;
use Feierstoff\ToolboxBundle\Exception\NotFoundException;
use Feierstoff\ToolboxBundle\Exception\UnauthorizedException;
use Feierstoff\ToolboxBundle\Exception\ViolationException;
use Feierstoff\ToolboxBundle\Response\ExceptionResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener {

    public function __construct(
        private string $env
    ) {}

    public function __invoke(ExceptionEvent $event): void {
        $exception = $event->getThrowable();

        switch (true) {
            case $exception instanceof NotFoundException:
                $event->setResponse(new ExceptionResponse(null, ExceptionResponse::NOT_FOUND));
                return;

            case $exception instanceof ForbiddenException:
                $event->setResponse(new ExceptionResponse(null, ExceptionResponse::FORBIDDEN));
                return;

            case $exception instanceof ViolationException:
                $event->setResponse(new ExceptionResponse($exception->getResponse(), ExceptionResponse::VIOLATION));
                return;

            case $exception instanceof UnauthorizedException:
                $event->setResponse(new ExceptionResponse(null, ExceptionResponse::UNAUTHORIZED));
                return;

            case $exception instanceof BadRequestException:
                $event->setResponse(new ExceptionResponse($exception->getResponse(), ExceptionResponse::BAD_REQUEST));
                return;

            default:
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