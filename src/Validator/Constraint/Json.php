<?php

namespace Feierstoff\ToolboxBundle\Validator\Constraint;

use Feierstoff\ToolboxBundle\Validator\ConstraintInterface;
use Feierstoff\ToolboxBundle\Validator\Violation;
use const JSON_ERROR_NONE;

class Json extends Constraint implements ConstraintInterface {

    public function __construct(
        private string $message = "This value is not a valid json string.",
        private bool $condition = true
    ) {
        parent::__construct(Constraint::JSON, $message, $condition);
    }

    /**
     * @param mixed $value
     * @return Violation|null
     */
    public function validate(mixed $value): ?Violation {
        if ($this->condition) {
            $valid = false;
            if (is_string($value)) {
                json_decode($value);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $valid = true;
                }
            }

            if (!$valid) return $this->getViolation();
        }

        return null;
    }

}