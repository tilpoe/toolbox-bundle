<?php

namespace Feierstoff\ToolboxBundle\ApiGenerator\Attribute;

use Feierstoff\ToolboxBundle\Exception\InternalServerException;
use Feierstoff\ToolboxBundle\Helper\DateTime;
use Feierstoff\ToolboxBundle\Validator\Constraint\NotBlank;
use Feierstoff\ToolboxBundle\Validator\Constraint\OneOf;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD)]
class Parameter {

    public const TYPE_DATETIME = "datetime";
    public const TYPE_BOOL = "bool";
    public const TYPE_INT = "int";
    public const TYPE_STRING = "string";
    public const TYPE_ARRAY = "array";
    public const TYPE_FILE = "file";
    public const TYPE_FLOAT = "float";

    private array $validTypes = [
        self::TYPE_INT,
        self::TYPE_BOOL,
        self::TYPE_INT,
        self::TYPE_STRING,
        self::TYPE_ARRAY,
        self::TYPE_FILE,
        self::TYPE_FLOAT,
        self::TYPE_DATETIME
    ];

    public function __construct(
        private readonly string $key,
        private readonly mixed $default = null,
        private readonly string $type = "",
        private readonly bool $required = false,
        private array $constraints = [],
        private string $description = "",
        private readonly bool $docOnly = false
    ) {
        // validate given type
        if ($this->type !== "" && !in_array($this->type, $this->validTypes)) {
            throw new InternalServerException("Invalid type {$this->type}.");
        }

        if ($this->required) {
            $this->constraints[] = new NotBlank();
        }

        $this->description = trim($this->description);
    }

    public function getOneOfForDocs(): ?array {
        foreach ($this->constraints as $constraint) {
            if ($constraint instanceof OneOf) {
                return $constraint->getValues();
            }
        }

        return null;
    }

    public function getKey(): string {
        return $this->key;
    }

    public function getDefault(): mixed {
        switch (true) {
            case $this->type == self::TYPE_DATETIME && $this->default !== null:
                return DateTime::createImmutable()->format($this->default);

            default:
                return $this->default;
        }
    }

    public function getDefaultForDocs(): ?string {
        switch ($this->type) {
            case self::TYPE_BOOL:
                if ($this->default) return "true";
                else return "false";

            case self::TYPE_ARRAY:
                return "[" . implode(",", $this->default) . "]";

            default:
                return $this->default;
        }
    }

    public function getType(): string {
        return $this->type;
    }

    public function getConstraints(): array {
        return $this->constraints;
    }

    public function getRequired(): bool {
        return $this->required;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getDocOnly(): bool {
        return $this->docOnly;
    }
}