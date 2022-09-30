<?php

namespace Feierstoff\ToolboxBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class OkResponse extends JsonResponse {

    public const CODE = 200;

    function __construct(?array $data = null) {
        parent::__construct($data, self::CODE);
    }

}