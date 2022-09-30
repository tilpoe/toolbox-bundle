<?php

namespace Feierstoff\ToolboxBundle\ApiGenerator\Attribute;

use Feierstoff\ToolboxBundle\Exception\InternalServerException;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD)]
class Response {

    public const OK = 200;
    public const BAD_REQUEST = 400;
    public const NOT_FOUND = 404;
    public const FORBIDDEN = 403;
    public const UNAUTHORIZED = 401;
    public const CREATED = 201;
    public const NO_CONTENT = 204;
    public const VIOLATION = 422;

    private array $validCodes = [
        self::OK,
        self::BAD_REQUEST,
        self::NO_CONTENT,
        self::NOT_FOUND,
        self::FORBIDDEN,
        self::UNAUTHORIZED,
        self::CREATED,
        self::VIOLATION
    ];

    public function __construct(
        private readonly int $code,
        private readonly string $description = "",
        private readonly ?array $example = null
    ) {
        if (!in_array($this->code, $this->validCodes)) {
            throw new InternalServerException("Invalid code.");
        }
    }

    public function getCode(): int {
        return $this->code;
    }

    public function getDescription(): string {
        return trim($this->description);
    }

    public function getExample(): ?array {
        return $this->example;
    }

}