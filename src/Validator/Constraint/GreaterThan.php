<?php

namespace Feierstoff\ToolboxBundle\Validator\Constraint;

use Feierstoff\ToolboxBundle\Validator\ConstraintInterface;
use Feierstoff\ToolboxBundle\Validator\Violation;

class GreaterThan extends Constraint implements ConstraintInterface {

    public function __construct(
        private int $min,
        private string $message = "This value has to be greater than #min.",
        private bool $condition = true
    ) {
        $this->message = str_replace("#min", $this->min, $this->message);
        parent::__construct(Constraint::GREATER_THAN, $this->message, $this->condition);
    }

    public function validate(mixed $value): ?Violation {
        if ($this->condition) {
            $value = filter_var($value, FILTER_VALIDATE_INT) ?? null;

            if ($value == null) return null;
            $valid = false;

            try {
                if (is_int($value) && $value > $this->min) {
                    $valid = true;
                }
            } catch (\Exception) {

            }

            return $valid ? null : $this->getViolation();
        }

        return null;
    }

}