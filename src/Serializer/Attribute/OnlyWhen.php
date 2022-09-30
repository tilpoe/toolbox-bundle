<?php

namespace Feierstoff\ToolboxBundle\Serializer\Attribute;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD)]
class OnlyWhen {
    public function __construct(private mixed $privileges) {
        if (!is_array($this->privileges)) {
            $this->privileges = [$this->privileges];
        }
    }

    /**
     * @param string[] $userPrivileges
     */
    public function check(array $userPrivileges): bool {
        $privileged = true;
        foreach ($this->privileges as $neededPrivilege) {
            if (!in_array($neededPrivilege, $userPrivileges)) {
                $privileged = false;
            }
        }

        return $privileged;
    }
}