<?php

namespace Frosas\MiscBundle;
 
use Doctrine\ORM\EntityManager;

class DoctrineOrmUtils
{
    private $entityManager;

    function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    function entityPropertiesHaveChanged($entity, $properties, $options = null)
    {
        foreach ((array) $properties as $property) {
            list($original, $current) = $this->getEntityPropertyValues($entity, $property, $options);
            if ($original !== $current) return true;
        }
    }

    function getEntityPropertyValues($entity, $property, $options = null)
    {
        $options = (array) $options + array('computeChangeset' => false);

        // Required if executed before onFlush or entity changed since then
        if ($options['computeChangeset']) $this->computeEntityChangeset($entity);

        $changeset = $this->entityManager->getUnitOfWork()->getEntityChangeSet($entity);
        if (isset($changeset[$property])) return $changeset[$property]; // array($original, $current)

        $actualValue = $this->entityMetadata($entity)->getFieldValue($entity, $property);
        return array($actualValue, $actualValue);
    }

    private function computeEntityChangeset($entity)
    {
        $this->entityManager->getUnitOfWork()->computeChangeSet($this->entityMetadata($entity), $entity);
    }

    /** @return \Doctrine\ORM\Mapping\ClassMetadata */
    private function entityMetadata($entity)
    {
        return $this->entityManager->getClassMetadata(get_class($entity));
    }
}