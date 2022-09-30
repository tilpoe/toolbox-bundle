<?php

namespace Feierstoff\ToolboxBundle\Exception;

class InternalServerException extends \Exception {

    public function __construct(string $message = "", public array $data = []) {
        parent::__construct($message);
    }

}