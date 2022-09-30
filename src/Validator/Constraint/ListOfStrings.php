<?php

namespace Feierstoff\ToolboxBundle\Validator\Constraint;

use Feierstoff\ToolboxBundle\Validator\ConstraintInterface;
use Feierstoff\ToolboxBundle\Validator\Violation;
use const JSON_ERROR_NONE;

class ListOfStrings extends Constraint implements ConstraintInterface {

    public function __construct(
        private string $message = "This value should be a list of strings.",
        private bool $condition = true
    ) {
        parent::__construct(Constraint::LIST_OF_STRINGS, $this->message, $this->condition);
    }

    public function validate(mixed $value): ?Violation {
/*        if (gettype($value) === "string") {
            $value = json_encode($value);
            $value = json_decode($value);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return $this->getViolation();
            }
        }

        echo is_array($value) == true ? "moin" : "no";*/

        if (gettype($value) === "array") {
            $allStrings = true;
            foreach ($value as $item) {
                if (gettype($item) !== "string") {
                    $allStrings = false;
                    break;
                }
            }

            if ($allStrings) {
                return null;
            }
        }

        return $this->getViolation();
    }

}