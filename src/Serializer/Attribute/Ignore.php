<?php

namespace Feierstoff\ToolboxBundle\Serializer\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Ignore {

    public function __construct() {}

}