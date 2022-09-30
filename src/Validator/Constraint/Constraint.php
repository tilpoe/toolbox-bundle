<?php

namespace Feierstoff\ToolboxBundle\Validator\Constraint;

use Feierstoff\ToolboxBundle\Validator\Violation;
use JetBrains\PhpStorm\ExpectedValues;
use JetBrains\PhpStorm\Pure;

class Constraint {

    protected const CUSTOM = "Custom";
    protected const DATE = "Date";
    protected const DATETIME = "DateTime";
    protected const EMAIL = "E-Mail";
    protected const GREATER_THAN = "GreaterThan";
    protected const JSON = "Json";
    protected const LENGTH = "Length";
    protected const LESS_THAN = "LessThan";
    protected const LIST_OF_STRINGS = "ListOfStrings";
    protected const NOT_BLANK = "NotBlank";
    protected const ONE_OF = "OneOf";
    protected const TIME = "Time";
    protected const URL = "URL";

    public function __construct(
        private string $constraint,
        private string $message,
        private bool $condition
    ) {

    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): self {
        $this->message = $message;
        return $this;
    }

    protected function getViolation(): Violation {
        return new Violation(message: $this->message, constraint: $this->constraint);
    }

}