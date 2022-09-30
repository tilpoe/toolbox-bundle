<?php

namespace Feierstoff\ToolboxBundle\Auth\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD)]
class AuthNeeded {

    public function __construct(public bool $nullable = false) {}

}