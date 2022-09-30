<?php

namespace Feierstoff\ToolboxBundle\Validator;

interface ConstraintInterface {
    public function validate(mixed $value): ?Violation;
}