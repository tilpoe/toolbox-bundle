<?php

namespace Feierstoff\ToolboxBundle\Auth\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Feierstoff\ToolboxBundle\Auth\Attribute\AuthNeeded;
use Feierstoff\ToolboxBundle\Auth\Attribute\HasPrivilege;
use Feierstoff\ToolboxBundle\Auth\Authenticator;
use Feierstoff\ToolboxBundle\EntityInterface\UserEntityInterface;
use Feierstoff\ToolboxBundle\Exception\NotFoundException;
use Feierstoff\ToolboxBundle\Exception\UnauthorizedException;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class AccessControlListener {

    public function __construct(
        private readonly Authenticator $authenticator,
        private readonly EntityManagerInterface $em,
        private readonly string $conf_user_entity
    ) {}

    public function __invoke(ControllerEvent $event) {
        if (!is_array($event->getController())) {
            return;
        }

        $method = new \ReflectionMethod($event->getController()[0], $event->getController()[1]);

        $authNeeded = $method->getAttributes(AuthNeeded::class);
        $authNeeded = !empty($authNeeded) ? $authNeeded[0]->newInstance() : null;
        $neededPrivileges = $method->getAttributes(HasPrivilege::class);

        if ($authNeeded || sizeof($neededPrivileges) > 0) {
            $userId = $this->authenticator->validateRequest($event->getRequest());

            /** @var UserEntityInterface $user */
            $user = $this->em->getRepository($this->conf_user_entity)->find($userId);
            if (!$user) {
                throw new UnauthorizedException();
            }

            $event->getRequest()->attributes->set("user", $user);

            if (sizeof($neededPrivileges) > 0) {
                $privileged = false;
                foreach ($neededPrivileges as $privilegeCheck) {
                    $privilegeCheck = $privilegeCheck->newInstance();
                    if ($privilegeCheck instanceof HasPrivilege) {
                        $privileged = $privilegeCheck->check($user->getPrivileges());
                    }

                    if ($privileged) break;
                }
                if (!$privileged) {
                    throw new NotFoundException();
                }
            }
        }
    }

}