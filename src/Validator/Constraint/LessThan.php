<?php

namespace Feierstoff\ToolboxBundle\Validator\Constraint;

use Feierstoff\ToolboxBundle\Validator\ConstraintInterface;
use Feierstoff\ToolboxBundle\Validator\Violation;

class LessThan extends Constraint implements ConstraintInterface {

    public function __construct(
        private int $max,
        private string $message = "This value has to be less than #max.",
        private bool $condition = true
    ) {
        $this->message = str_replace("#max", $this->max, $this->message);
        parent::__construct(Constraint::LESS_THAN, $this->message, $this->condition);
    }

    public function validate(mixed $value): ?Violation {
        if ($this->condition) {
            $value = filter_var($value, FILTER_VALIDATE_INT) ?? null;

            if ($value === null) {
                return null;
            }
            $valid = false;

            try {
                if (is_int($value) && $value < $this->max) {
                    $valid = true;
                }
            } catch (\Exception) {}

            return $valid ? null : $this->getViolation();
        }

        return null;
    }

}