<?php

namespace Feierstoff\ToolboxBundle\Validator\Constraint;

use Feierstoff\ToolboxBundle\Validator\ConstraintInterface;
use Feierstoff\ToolboxBundle\Validator\Violation;

class NotBlank extends Constraint implements ConstraintInterface {

    public function __construct(
        private string $message = "This value should not be blank.",
        private bool $condition = true
    ) {
        parent::__construct(Constraint::NOT_BLANK, $message, $condition);
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
            return $value === null ? $this->getViolation() : null;
        }

        return null;
    }

}