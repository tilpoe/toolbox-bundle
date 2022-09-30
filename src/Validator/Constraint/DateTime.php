<?php

namespace Feierstoff\ToolboxBundle\Validator\Constraint;

use Exception;
use Feierstoff\ToolboxBundle\Validator\ConstraintInterface;
use Feierstoff\ToolboxBundle\Validator\Violation;
use JetBrains\PhpStorm\Pure;

class DateTime extends Constraint implements ConstraintInterface {

    #[Pure]
    public function __construct(
        private string $message = "This value is not a valid datetime.",
        private bool $condition = true
    ) {
        parent::__construct(Constraint::DATETIME, $message, $condition);
    }

    /**
     * @param mixed $value
     * @return Violation|null
     */
    public function validate(mixed $value): ?Violation {
        if ($value instanceof \DateTimeImmutable) {
            return null;
        }

        if ($this->condition) {
            if ($value == null) return null;
            $valid = false;

            try {
                if (is_string($value)) {
                    new \DateTime($value);
                    $valid = true;
                }
            } catch (Exception) {

            }

            return $valid ? null : $this->getViolation();
        }

        return null;
    }

}