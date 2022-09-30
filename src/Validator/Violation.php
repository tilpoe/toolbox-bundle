<?php

namespace Feierstoff\ToolboxBundle\Validator;

use Feierstoff\ToolboxBundle\Exception\InternalServerException;
use Feierstoff\ToolboxBundle\Exception\ViolationException;
use Feierstoff\ToolboxBundle\Validator\Constraint\Constraint;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\ExpectedValues;

class Violation {

    /**
     * @param string $message Error message that describes the violation.
     * @param string $constraint Name of the constraint that was violated.
     * @param string|null $target Key of the value that was violated.
     */
    function __construct(
        private string $message,
        private ?string $target = "",
        private string $constraint = "Custom",
    ) {

    }

    #[ArrayShape([
        "constraint" => "string",
        "message" => "string",
        "target" => "string"
    ])]
    public function toArray(): array {
        return [
            "constraint" => $this->constraint,
            "message" => $this->message,
            "target" => $this->target
        ];
    }

    /**
     * @return string
     */
    public function getTarget(): string {
        return $this->target;
    }

    /**
     * @param string|null $target
     */
    public function setTarget(?string $target): void
    {
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getMessage(): string {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getConstraint(): string {
        return $this->constraint;
    }

    /**
     * Throw exception with the violation data.
     * @throws ViolationException
     */
    public function violate() {
        throw new ViolationException([$this]);
    }

    /**
     * @throws InternalServerException
     */
    public function violateInternal() {
        throw new InternalServerException($this->message);
    }

}