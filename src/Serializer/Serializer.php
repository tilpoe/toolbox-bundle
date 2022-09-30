<?php

namespace Feierstoff\ToolboxBundle\Serializer;

use App\ShopwareEntity\SCoreStates;
use Doctrine\Common\Collections\Collection;
use Feierstoff\ToolboxBundle\EntityInterface\UserEntityInterface;
use Feierstoff\ToolboxBundle\Exception\InternalServerException;
use Feierstoff\ToolboxBundle\Serializer\Attribute\FormatDateTime;
use Feierstoff\ToolboxBundle\Serializer\Attribute\Ignore;
use Feierstoff\ToolboxBundle\Serializer\Attribute\HasPrivilege;
use Feierstoff\ToolboxBundle\Serializer\Attribute\OnlyWhen;
use ReflectionException;

class Serializer {

    public function __construct() {}

    public function deserialize(string $className, array $params, $maxDepth = 2): mixed {
        try {
            return $this->deserializeRecursive($className, $params, 1, $maxDepth);
        } catch (\Exception $e) {
            throw new InternalServerException("Error deserializing entity: {$e->getMessage()}");
        }
    }

    private function deserializeRecursive(string $className, array $params, int $round, int $maxDepth): mixed {
        $reflectionClass = new \ReflectionClass($className);

        $constructor = [];
        foreach ($reflectionClass->getConstructor()->getParameters() as $constructorArg) {
            $name = $constructorArg->getName();
            if (!$constructorArg->allowsNull() && !array_key_exists($name, $params)) {
                throw new \Exception("Missing argument {$name} for object of type $className");
            }

            $valueType = gettype($params[$name]);
            if ($valueType != $constructorArg->getType()) {
                if (str_contains($constructorArg->getType()->getName(), "Entity") && $valueType == "array") {
                    $constructor[$name] = $this->deserializeRecursive($constructorArg->getType()->getName(), $params[$name], $round + 1, $maxDepth);
                }
            }

/*            $valueType = gettype($params[$name]);
            if ($valueType != $constructorArg->getType()) {
                if (str_contains($constructorArg->getType()->getName(), "Entity")) {
                    if (gettype($valueType) == "array") {

                    }
                }
            }*/

        }
    }

    /**
     * Checks for every property of the given entity
     *
     * @param $object - the entity
     * @param $params - possible updates of the properties of the entity
     * @throws InternalServerException
     */
    public function update(mixed &$object, $params) {
        try {
            $methods = (new \ReflectionClass(get_class($object)))->getMethods();
            foreach ($methods as $method) {
                $methodName = $method->getName();
                // we extract the setters because we want to update the entity with the params given
                if (str_starts_with($methodName, "set")) {
                    // extract the property name
                    $property = strtolower(substr($methodName, 3, 1)) . substr($methodName, 4);

                    if (array_key_exists($property, $params)) {
                        // TODO: Validation
                        $method->invokeArgs($object, [$property => $params[$property]]);
                    }
                }
            }
        } catch (ReflectionException $e) {
            throw new InternalServerException("Error deserializing entity: {$e->getMessage()}.");
        }
    }

    /**
     * Given an entity (or an array of entities) $data, this function returns
     * an array of the serialized entity properties.
     *
     * @param mixed $data
     * @return array
     * @throws InternalServerException
     */
    public function serialize(mixed $data, int $maxDepth = 2, UserEntityInterface $user = null): array {
        try {
            if (gettype($data) === "array" || $data instanceof Collection) {
                $result = [];
                foreach ($data as $entity) {
                    $result[] = $this->serializeRecursive($entity, 1, $maxDepth, $user);
                }
                return $result;
            } else {
                return $this->serializeRecursive($data, 1, $maxDepth, $user);
            }
        } catch (ReflectionException $e) {
            throw new InternalServerException("Error serializing entity: {$e->getMessage()}.");
        }
    }

    /**
     * @throws ReflectionException
     */
    private function serializeRecursive(mixed $data, int $round, int $maxDepth, ?UserEntityInterface $user): ?array {
        // return null if value of property is null
        if ($data == null) return null;

        $className = get_class($data);

        // convert proxy to real entity
/*        if (str_starts_with($className, "Proxies\\__CG__\\")) {
            $className = substr($className, 15);
        }*/

        $methods = (new \ReflectionClass($className))->getMethods();

        $result = [];
        foreach ($methods as $method) {
            // we want to serialize every getter method of the entity
            $methodName = $method->getName();

            if (str_starts_with($methodName, "get")) {
                // isolate the affected property from the getter name
                $property = lcfirst(substr($methodName, 3));
                $returnType = $method->getReturnType()?->getName();

                // check attributes
                $ignore = sizeof($method->getAttributes(Ignore::class)) > 0;
                if ($ignore) {
                    continue;
                }

                $onlyWhen = $method->getAttributes(OnlyWhen::class);
                $privileged = sizeof($onlyWhen) == 0;
                foreach ($onlyWhen as $privilegeCheck) {
                    if (!$user) continue;
                    $privilegeCheck = $privilegeCheck->newInstance();
                    if ($privilegeCheck instanceof OnlyWhen) {
                        $privileged = $privilegeCheck->check($user->getPrivileges());
                    }

                    if ($privileged) break;
                }
                if (!$privileged) continue;

                switch ($returnType) {
                    // primitive types may be returned  on the spot
                    case "string":
                    case "int":
                    case "float":
                    case "bool":
                        $result[$property] = $method->invoke($data);
                        break;
                    // collections of sub entities will only be serialized recursively
                    // if the property is part of the main entity that was serialized
                    // otherwise the property will be ignored
                    case "Doctrine\Common\Collections\Collection":
                        $subEntities = $method->invoke($data);
                        $collection = [];
                        if ($subEntities instanceof Collection) {
                            foreach ($subEntities->toArray() as $entity) {
                                if ($round < $maxDepth) {
                                    $collection[] = $this->serializeRecursive($entity, $round + 1, $maxDepth, $user);
                                }
                            }
                        }

                        if ($round < $maxDepth) {
                            $result[$property] = $collection;
                        }
                        break;
                    // A DateTime object will be formatted like "Y-m-d H:i:s" by default
                    // You can change the format by adding the <FormatDateTime> attribute
                    // to the corresponding getter method of the entity
                    case "DateTime":
                    case "DateTimeImmutable":
                        $datetime = $method->invoke($data);
                        if ($datetime) {
                            $attributes = $method->getAttributes(FormatDateTime::class);
                            $formatted = false;
                            if (sizeof($attributes) > 0) {
                                $formatter = $attributes[0]->newInstance();
                                if ($formatter instanceof FormatDateTime) {
                                    $datetime = $datetime->format($formatter->format);
                                    $formatted = true;
                                }
                            }

                            if (!$formatted) {
                                $datetime = $datetime->format("Y-m-d H:i:s");
                            }
                        }
                        $result[$property] = $datetime;
                        break;
                    default:
                        switch (true) {
                            // sub entities should be serialized recursively
                            // but sub entities of sub entities should only return a reference (the id property)
                            case str_contains($returnType, "Entity"):
                            case str_contains($returnType, "self"):
                                $subEntity = $method->invoke($data);
                                if ($subEntity == null) {
                                    $result[$property] = null;
                                    break;
                                }

                                $result[$property] = $round < $maxDepth ? $this->serializeRecursive($subEntity, $round + 1, $maxDepth, $user) : ["id" => $subEntity->getId()];
                                break;
                            case $returnType === "array":
                                $subEntities = $method->invoke($data);
                                $strings = [];
                                foreach ($subEntities as $entity) {
                                    $strings[] = $entity;
                                }
                                $result[$property] = $strings;
                                break;
                        }
                }
            }
        }

        return $result;
    }

}