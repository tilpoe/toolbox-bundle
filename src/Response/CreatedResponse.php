<?php

namespace Feierstoff\ToolboxBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class CreatedResponse extends JsonResponse {

    public const CODE = 201;

    function __construct(?array $data = null) {
        parent::__construct($data, self::CODE);
    }

}