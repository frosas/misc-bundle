<?php

namespace Frosas\MiscBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\EntityManager;
use Frosas\Collection;
use Symfony\Component\Validator\Validator;

/**
 * Validates the entities being flushed
 */
class EntityValidation
{
    private $validator;

    function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    function onFlush(OnFlushEventArgs $args)
    {
        foreach ($this->getEntitiesToValidate($args->getEntityManager()) as $entity) {
            if (count($violations = $this->validator->validate($entity))) {
                $message = "Invalid entity {$this->objectToString($entity)}:\n";
                $message .= implode("\n", Collection::map($violations, function($violation) {
                    return "- {$violation->getPropertyPath()}: {$violation->getMessage()}";
                }));
                throw new \InvalidArgumentException($message);
            }
        }
    }

    private function getEntitiesToValidate(EntityManager $entityManager)
    {
        return array_merge(
            $entityManager->getUnitOfWork()->getScheduledEntityInsertions(),
            $entityManager->getUnitOfWork()->getScheduledEntityUpdates());
    }

    private function objectToString($object)
    {
        if (method_exists($object, '__toString')) return (string) $object;

        $string = get_class($object);
        $properties= array();
        $objectReflection = new \ReflectionObject($object);
        foreach ($objectReflection->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);
            if (is_object($value)) $value = "(object)";
            else if (is_string($value)) $value = "\"$value\"";
            else if (is_bool($value)) $value = $value ? "true" : "false";
            else if ($value === null) $value = "null";
            else if (is_array($value)) $value = "(array)";
            $properties[] = "{$property->getName()}: $value";
        }
        if ($properties) $string .= " (" . implode(", ", $properties) . ")";
        return $string;
    }
}
