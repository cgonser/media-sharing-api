<?php

declare(strict_types=1);

namespace App\Core\ORM;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;

class SoftDeletableFilter extends SQLFilter
{
    protected $entityManager;
    protected $disabled = [];

    /**
     * Add the soft deletable filter.
     *
     * @param $targetTableAlias
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        $class = $targetEntity->getName();
        if (array_key_exists($class, $this->disabled) && true === $this->disabled[$class]) {
            return '';
        }
        if (
            array_key_exists($targetEntity->rootEntityName, $this->disabled)
            && true === $this->disabled[$targetEntity->rootEntityName]
        ) {
            return '';
        } elseif (! $targetEntity->hasField('deletedAt')) {
            return '';
        }

        $conn = $this->getEntityManager()
            ->getConnection();
        $platform = $conn->getDatabasePlatform();
        $column = $targetEntity->getQuotedColumnName('deletedAt', $platform);

        return $platform->getIsNullExpression($targetTableAlias.'.'.$column);
    }

    public function disableForEntity($class): void
    {
        $this->disabled[$class] = true;
    }

    public function enableForEntity($class): void
    {
        $this->disabled[$class] = false;
    }

    protected function getEntityManager()
    {
        if (null === $this->entityManager) {
            $refl = new \ReflectionProperty(SQLFilter::class, 'em');
            $refl->setAccessible(true);
            $this->entityManager = $refl->getValue($this);
        }

        return $this->entityManager;
    }
}
