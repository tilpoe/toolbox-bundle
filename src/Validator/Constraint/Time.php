<?php

namespace Feierstoff\ToolboxBundle\Validator\Constraint;

use Exception;
use Feierstoff\ToolboxBundle\Validator\ConstraintInterface;
use Feierstoff\ToolboxBundle\Validator\Violation;

class Time extends Constraint implements ConstraintInterface {

    private const PATTERN = '/^(\d{2}):(\d{2})(:(\d{2}))?$/';

    public function __construct(
        private string $message = "This value is not a valid time.",
        private bool $condition = true
    ) {
        parent::__construct(Constraint::TIME, $message, $condition);
    }

    /**
     * @param mixed $value
     * @return Violation|null
     */
    public function validate(mixed $value): ?Violation {
        if ($value instanceof \DateTimeImmutable) return null;

        if ($this->condition) {
            $valid = false;

            try {
                if (is_string($value) && preg_match(self::PATTERN, $value)) {
                    new \DateTime($value);
                    $valid = true;
                }
            } catch (Exception) {

            }

            if (!$valid) {
                return $this->getViolation();
            }
        }

        return null;
    }

}