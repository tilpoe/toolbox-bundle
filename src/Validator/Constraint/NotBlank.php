<?php

namespace Feierstoff\ToolboxBundle\Validator\Constraint;

use Feierstoff\ToolboxBundle\Validator\ConstraintInterface;
use Feierstoff\ToolboxBundle\Validator\Violation;

class NotBlank extends Constraint implements ConstraintInterface {

    public function __construct(
        private string $message = "",
        private bool $empty = false,
        private bool $condition = true
    ) {
        parent::__construct(Constraint::NOT_BLANK, $message == "" ? "This value should not be blank." : $message, $condition);
    }

    /**
     * @param mixed $value
     * @return Violation|null
     */
    public function validate(mixed $value): ?Violation {
        if (is_array($value)) {
            return null;
        }

        if ($this->condition) {
            return ($value === null || $this->empty && empty($value)) ? $this->getViolation() : null;
        }

        return null;
    }

}