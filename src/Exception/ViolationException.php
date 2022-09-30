<?php

namespace Feierstoff\ToolboxBundle\Exception;

class ViolationException extends \Exception {

    public function __construct(
        private ?array $violations = [],
        string $message = "",
        private string $snippet = ""
    ) {
        parent::__construct($message);
    }

    public function getResponse(): array {
        $response = [];

        if ($this->violations) {
            $response["data"] = ["violations" => $this->violations];
        }

        if ($this->message) {
            $response["message"] = $this->message;
            $response["snippet"] = $this->snippet;
        }

        return $response;
    }

}