<?php

namespace Feierstoff\ToolboxBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class ExceptionResponse extends JsonResponse {

    public const INTERNAL = self::HTTP_INTERNAL_SERVER_ERROR;
    public const VIOLATION = self::HTTP_UNPROCESSABLE_ENTITY;
    public const NOT_FOUND = self::HTTP_NOT_FOUND;
    public const FORBIDDEN = self::HTTP_FORBIDDEN;
    public const UNAUTHORIZED = self::HTTP_UNAUTHORIZED;
    public const BAD_REQUEST = self::HTTP_BAD_REQUEST;

    function __construct(?array $data = null, int $code = self::INTERNAL) {
        parent::__construct($data, $code);
    }

}