<?php

namespace Feierstoff\ToolboxBundle\Exception;

class BadRequestException extends \Exception {

    public function __construct(
        string $message = "",
        private string $snippet = "",
        private ?array $data = null
    ) {
        parent::__construct($message);
    }

    public function getResponse(): array {
        $data = [
            "message" => $this->message,
            "snippet" => $this->snippet
        ];

        if ($this->data) $data["data"] = $this->data;

        return $data;
    }

}