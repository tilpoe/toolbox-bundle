<?php

namespace Feierstoff\ToolboxBundle\Validator;

use Feierstoff\ToolboxBundle\Exception\InternalServerException;
use Feierstoff\ToolboxBundle\Exception\ViolationException;
use Feierstoff\ToolboxBundle\Validator\Constraint\NotBlank;

class Validator {

    /**
     * @var array
     */
    private array $collection = [];

    /**
     * @var Violation[]
     */
    private array $violations = [];

    /**
     * Initialize a set of constraints to validate.
     *
     * @param array|null $reference array of values that are checked against the constraints
     */
    public function __construct(
        private ?array $reference = null
    ) {

    }

    /**
     * Set the reference values after instantiating the Validator.
     *
     * @param array $reference
     * @return $this
     */
    public function setReference(array $reference): self {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @param string $target
     * @param array $constraints
     * @return $this
     */
    public function add(string $target, array $constraints): self {
        if (array_key_exists($target, $this->collection)) {
            // if constraints for the key already exist: concatenate both lists of constraints
            $this->collection[$target] = array_merge($this->collection[$target], $constraints);
        } else {
            $this->collection[$target] = $constraints;
        }

        return $this;
    }

    public function getCollection(): array {
        return $this->collection;
    }

    /**
     * Checks for every key of constraints the reference array and it's values.
     * If a constraint isn't valid, a violation is added to the violation property.
     * Constraints that are still left to be checked will be ignored.
     */
    public function validate(): self {
        if (!$this->reference) {
            throw new InternalServerException("No reference values for validation.");
        }

        // Reset all violations from possible previous validations
        $this->violations = [];

        foreach ($this->collection as $target => $constraints) {
            if (array_key_exists($target, $this->reference)) {
                // if value for given constraint key is found in the reference: validate all constraints
                /** @var ConstraintInterface $constraint */
                foreach ($constraints as $constraint) {
                    $violation = $constraint->validate($this->reference[$target]);
                    if ($violation) {
                        // if constraint was violated, add error and go to next key
                        $violation->setTarget($target);
                        $this->violations[] = $violation;
                        break;
                    }
                }
            } else {
                // if value was not found but there is a NotBlank constraint, an error needs to be added
                foreach ($constraints as $constraint) {
                    if ($constraint instanceof NotBlank) {
                        $violation = $constraint->validate("");
                        $violation->setTarget($target);
                        $this->violations[] = $violation;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Check if there are any violations from the last validation.
     * @return bool
     */
    public function isValid(): bool {
        return sizeof($this->violations) == 0;
    }

    /**
     * @return Violation[]
     */
    public function getViolations(): array {
        return $this->violations;
    }

    /**
     * Throws an error with the current violations.
     *
     * @param bool $withValidation Validates the constraints beforehand
     * @return $this
     * @throws ViolationException
     */
    public function violate(bool $withValidation = true): self {
        if ($withValidation) {
            $this->validate();
        }

        if (!$this->isValid()) {
            $violations = [];
            foreach ($this->violations as $violation) {
                $violations[] = $violation->toArray();
            }

            throw new ViolationException($violations);
        }

        return $this;
    }

    /**
     * @return $this
     * @throws InternalServerException
     */
    public function violateInternal(): self {
        $this->validate();
        if (!$this->isValid()) {
            throw new InternalServerException("Invalid values.");
        }
        return $this;
    }

}