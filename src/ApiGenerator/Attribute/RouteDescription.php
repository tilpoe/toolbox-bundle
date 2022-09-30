<?php

namespace Feierstoff\ToolboxBundle\ApiGenerator\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD)]
class RouteDescription {

    public function __construct(
        private readonly string $description
    ) {}

    public function getDescription(): string {
        return $this->description;
    }

}