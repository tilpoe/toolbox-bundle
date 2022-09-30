<?php

namespace Feierstoff\ToolboxBundle\Validator\Constraint;

use Feierstoff\ToolboxBundle\Validator\ConstraintInterface;
use Feierstoff\ToolboxBundle\Validator\Violation;

class Length extends Constraint implements ConstraintInterface {

    public function __construct(
        private int $min = -1,
        private int $max = -1,
        private string $minMsg = "This value has not enough characters.",
        private string $maxMsg = "This value has too many characters.",
        private string $message = "This value is invalid.",
        private bool $condition = true
    ) {
        parent::__construct(Constraint::LENGTH, $message, $condition);
    }

    /**
     * @param mixed $value
     * @return Violation|null
     */
    public function validate(mixed $value): ?Violation {
        if ($this->condition) {
            $valid = false;

            if (is_string($value)) {
                if (
                    $this->min > -1 && strlen($value) >= $this->min &&
                    $this->max > -1 && strlen($value) <= $this->max
                ) {
                    $valid = true;
                }
            }

            if (!$valid) return $this->getViolation();
        }

        return null;
    }

}
