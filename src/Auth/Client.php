<?php

namespace Feierstoff\ToolboxBundle\Auth;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;

class Client implements ClientEntityInterface {
    use ClientTrait;

    public function isConfidential(): bool {
        return true;
    }

    public function getIdentifier(): string {
        return "client";
    }
}