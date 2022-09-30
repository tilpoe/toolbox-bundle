<?php

namespace Feierstoff\ToolboxBundle\Validator\Constraint;

use Exception;
use Feierstoff\ToolboxBundle\Validator\ConstraintInterface;
use Feierstoff\ToolboxBundle\Validator\Violation;
use JetBrains\PhpStorm\Pure;
use DateTime;

class Date extends Constraint implements ConstraintInterface {

    private const PATTERN = '/^(?<year>\d{4})-(?<month>\d{2})-(?<day>\d{2})$/';

    #[Pure]
    public function __construct(
        private string $message = "This value is not a valid date.",
        private bool $condition = true
    ) {
        parent::__construct(Constraint::DATE, $message, $condition);
    }

    /**
     * @param mixed $value
     * @return Violation|null
     */
    public function validate(mixed $value): ?Violation {
        if ($value instanceof \DateTimeImmutable) return null;

        if ($this->condition) {
            if ($value == null) return null;
            $valid = false;

            try {
                if (is_string($value) && preg_match(self::PATTERN, $value)) {
                    new DateTime($value);
                    $valid = true;
                }
            } catch (Exception) {

            }

            return $valid ? null : $this->getViolation();
        }

        return null;
    }

}

