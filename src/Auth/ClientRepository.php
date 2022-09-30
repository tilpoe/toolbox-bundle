<?php

namespace Feierstoff\ToolboxBundle\Auth;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface {

    public function getClientEntity($clientIdentifier): Client {
        return new Client();
    }

    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool {
        return true;
    }
}