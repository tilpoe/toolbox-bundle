<?php

namespace Feierstoff\ToolboxBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class NoContentResponse extends JsonResponse {

    public const CODE = 204;

    function __construct(?array $data = null) {
        parent::__construct($data, self::CODE);
    }

}