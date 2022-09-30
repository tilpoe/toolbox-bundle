<?php

namespace Feierstoff\ToolboxBundle\Serializer\Attribute;

#[\Attribute]
class FormatDateTime {

    public function __construct(public string $format) {}

}
