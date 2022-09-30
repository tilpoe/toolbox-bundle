<?php

namespace Feierstoff\ToolboxBundle\Validator\Constraint;

use Feierstoff\ToolboxBundle\Validator\ConstraintInterface;
use Feierstoff\ToolboxBundle\Validator\Violation;

class Email extends Constraint implements ConstraintInterface {

    private const REG_MAIL = '/^[a-zA-Z0-9.!#$%&\'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/';

    public function __construct(
        private string $message = "This value is not a valid email address.",
        private bool $condition = true
    ) {
        parent::__construct(Constraint::EMAIL, $message, $condition);
    }

    /**
     * @param mixed $value
     * @return Violation|null
     */
    public function validate(mixed $value): ?Violation {
        if ($this->condition) {
            if (
                !is_string($value) ||
                !preg_match(self::REG_MAIL, $value) ||
                str_contains($value, "..") ||
                str_contains($value, ".@") ||
                !filter_var($value, FILTER_VALIDATE_EMAIL)
            ) {
                return $this->getViolation();
            }
        }

        return null;
    }

}