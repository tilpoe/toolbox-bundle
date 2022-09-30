<?php

namespace Feierstoff\ToolboxBundle\Validator\Constraint;

use Feierstoff\ToolboxBundle\Validator\ConstraintInterface;
use Feierstoff\ToolboxBundle\Validator\Violation;
use JetBrains\PhpStorm\ArrayShape;

class NotOneOf extends Constraint implements ConstraintInterface {

    public function __construct(
        private array $values,
        private string $message = "This value is not valid.",
        private bool $condition = true
    ) {
        parent::__construct(Constraint::ONE_OF, $message, $condition);
    }

    public function getValues(): array {
        return $this->values;
    }

    /**
     * @param mixed $value
     * @return Violation|null
     */
    public function validate(mixed $value): ?Violation {
        if ($this->condition) {
            if (in_array($value, $this->values)) {
                return $this->getViolation();
            }
        }

        return null;
    }

}

?>