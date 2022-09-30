<?php

namespace Feierstoff\ToolboxBundle\ApiGenerator;

use Doctrine\ORM\EntityManagerInterface;
use Feierstoff\ToolboxBundle\ApiGenerator\Attribute\Parameter;
use Feierstoff\ToolboxBundle\ApiGenerator\Parameter\InjectParameters;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class ApiAttributeListener {

    public function __construct(
        private readonly InjectParameters $injectParameters
    ) {}

    public function __invoke(ControllerEvent $event) {
        if (!$event->isMainRequest() || !is_array($event->getController())) {
            return;
        }

        $controllerFunction = new \ReflectionMethod($event->getController()[0], $event->getController()[1]);

        // Parameters
        $parameterAttributes = $controllerFunction->getAttributes(Parameter::class);
        $this->injectParameters->inject($event, $parameterAttributes);
    }

}