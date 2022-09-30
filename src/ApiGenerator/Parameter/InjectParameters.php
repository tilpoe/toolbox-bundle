<?php

namespace Feierstoff\ToolboxBundle\ApiGenerator\Parameter;

use Feierstoff\ToolboxBundle\ApiGenerator\Attribute\Parameter;
use Feierstoff\ToolboxBundle\Exception\BadRequestException;
use Feierstoff\ToolboxBundle\Exception\InternalServerException;
use Feierstoff\ToolboxBundle\Helper\DateTime;
use Feierstoff\ToolboxBundle\Validator\Validator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class InjectParameters {

    /**
     * @param ControllerEvent $event
     * @param \ReflectionAttribute[] $parameterAttributes
     * @return void
     */
    public function inject(ControllerEvent $event, array $parameterAttributes): void {
        $parsedParameters = [];
        $parameters = new Parameters($event->getRequest());

        /** @var Parameter[] $attributeInstances */
        $attributeInstances = [];

        $validator = new Validator();
        $countDocOnly = 0;
        foreach ($parameterAttributes as $attribute) {
            $attributeInstance = $attribute->newInstance();

            // get value from request
            if ($attributeInstance instanceof Parameter) {
                if ($attributeInstance->getDocOnly()) {
                    $countDocOnly++;
                    continue;
                }
                $attributeInstances[] = $attributeInstance;
                $parsedParameters[$attributeInstance->getKey()] = $parameters->getFromRequest($attributeInstance->getKey(), $attributeInstance->getDefault());
                $validator->add($attributeInstance->getKey(), $attributeInstance->getConstraints());
            }
        }

        if (sizeof($parameterAttributes) > $countDocOnly) {
            $validator->setReference($parsedParameters)->violate();
        }

        foreach ($attributeInstances as $attributeInstance) {
            $value = $parsedParameters[$attributeInstance->getKey()];

            if ($value === null) {
                break;
            }

            switch ($attributeInstance->getType()) {
                case Parameter::TYPE_DATETIME:
                    try {
                        $value = DateTime::createImmutable($value);
                    } catch (\Exception) {
                        throw new BadRequestException("Expected type DATETIME for attribute {$attributeInstance->getKey()}.");
                    }
                    break;

                case Parameter::TYPE_BOOL:
                    if ($value !== null) {
                        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    }
                    break;

                case Parameter::TYPE_INT:
                    $value = filter_var($value, FILTER_VALIDATE_INT);
                    if ($value === false) {
                        throw new BadRequestException("Expected type INT for attribute {$attributeInstance->getKey()}.");
                    }
                    break;

                case Parameter::TYPE_ARRAY:
                    if (!is_array($value)) {
                        throw new BadRequestException("Expected type ARRAY for attribute {$attributeInstance->getKey()}.");
                    }
                    break;

                case Parameter::TYPE_STRING:
                    if (!is_string($value)) {
                        throw new BadRequestException("Expected type STRING for attribute {$attributeInstance->getKey()}.");
                    }
                    break;

                case Parameter::TYPE_FILE:
                    if (!($value instanceof UploadedFile)) {
                        throw new BadRequestException("Expected type FILE for attribute {$attributeInstance->getKey()}.");
                    }
                    break;

                case Parameter::TYPE_FLOAT:
                    $value = filter_var($value, FILTER_VALIDATE_FLOAT);
                    if ($value === false) {
                        throw new BadRequestException("Expected type INT for attribute {$attributeInstance->getKey()}.");
                    }
                    break;
            }

            $parsedParameters[$attributeInstance->getKey()] = $value;
        }

        $parameters->setAll($parsedParameters);
        $event->getRequest()->attributes->set("parameters", $parameters);
    }

}