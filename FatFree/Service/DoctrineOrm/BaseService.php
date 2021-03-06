<?php declare(strict_types=1);

namespace FatFree\Service\DoctrineOrm;

use FatFree\Dao\DoctrineOrm;
use FatFree\Entity\DoctrineOrm\BaseEntity;
use FatFree\Service\ServiceException;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

abstract class BaseService extends DoctrineOrm
{
    /**
     * Method will get results from DB by given Entity definition
     * @param BaseEntity $entity
     * @return BaseEntity
     */
    public function get(BaseEntity $entity): BaseEntity
    {
        $result = $this->entityManager
            ->getRepository($entity->getClassName())
            ->find($entity);

        return $result ? $result : $entity;
    }

    /**
     * Method will get all results from DB by given Entity definition
     * @param BaseEntity $entity
     * @return array
     */
    public function getAll(BaseEntity $entity): array
    {
        $result = $this->entityManager
            ->getRepository($entity->getClassName())
            ->findAll($entity);

        return $result ? $result : [];
    }

    /**
     * Method will get entity by given keys
     * @param BaseEntity $entity
     * @param array $keys
     * @return BaseEntity
     */
    public function getOneByKeys(BaseEntity $entity, array $keys): BaseEntity
    {
        $result = $this->entityManager
            ->getRepository($entity->getClassName())
            ->findOneByKeys($entity, $keys);

        return $result ? $result : $entity;
    }

    /**
     * Method will get entities by given keys
     * @param BaseEntity $entity
     * @param array $keys
     * @return array
     */
    public function getAllByKeys(BaseEntity $entity, array $keys): array
    {
        $result = $this->entityManager
            ->getRepository($entity->getClassName())
            ->findByKeys($entity, $keys);

        return $result ? $result : [];
    }

    /**
     * Method will insert entity to DB
     * @param BaseEntity $entity
     * @param array $values
     * @param boolean $flush
     * @return BaseEntity
     */
    public function insert(BaseEntity $entity, array $values = [], $flush = true): BaseEntity
    {
        if (!empty($values)) {
            //Map data to entity
            $entity->fromArray($this->prepareAttributes($entity, $values));
        }

        $entity->setCreated(new \DateTime());

        if ($entity->isIdSet()) {
            $metadata = $this->entityManager->getClassMetaData($entity->getClassName());
            $metadata->setIdGenerator(new AssignedGenerator);
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        }
        $this->entityManager
            ->persist($entity);

        if ($flush) {
            $this->flush();
        }

        return $entity;
    }

    /**
     * Method will insert entity to DB when same entity doesnt exist.
     * @param BaseEntity $entity
     * @param array $values
     * @param boolean $flush
     * @return BaseEntity
     */
    public function insertIfNotExist(BaseEntity $entity, array $values = [], $flush = true): BaseEntity
    {
        if (!empty($values)) {
            //Map data to entity
            $entity->fromArray($this->prepareAttributes($entity, $values));
        }

        if (!self::exist($entity)) {
            $entity->setCreated(new \DateTime());

            if ($entity->isIdSet()) {
                $metadata = $this->entityManager->getClassMetaData($entity->getClassName());
                $metadata->setIdGenerator(new AssignedGenerator);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            }

            $this->entityManager
                ->persist($entity);
        }

        if ($flush) {
            $this->flush();
        }

        return $entity;
    }

    /**
     * Method will insert entity to DB when same entity doesnt exist. Existing entity will be checked by given keys
     * @param BaseEntity $entity
     * @param array $values
     * @param array $keys
     * @param bool $flush
     * @return BaseEntity
     */
    public function insertIfNotExistByKeys(BaseEntity $entity, array $values = [], array $keys, $flush = true): BaseEntity
    {
        if (!empty($values)) {
            //Map data to entity
            $entity->fromArray($this->prepareAttributes($entity, $values));
        }

        $foundEntity = $this->entityManager
            ->getRepository($entity->getClassName())
            ->findOneByKeys($entity, $keys);

        if (!$foundEntity) {
            $entity->setCreated(new \DateTime());

            if ($entity->isIdSet()) {
                $metadata = $this->entityManager->getClassMetaData($entity->getClassName());
                $metadata->setIdGenerator(new AssignedGenerator);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            }

            $this->entityManager
                ->persist($entity);

            if ($flush) {
                $this->flush();
            }

            return $entity;
        }

        return $foundEntity;
    }

    /**
     * Method will insert entity to DB when same entity doesnt exist.
     * @param BaseEntity $entity
     * @param array $values
     * @param boolean $flush
     * @return BaseEntity
     */
    public function mergeIfNotExist(BaseEntity $entity, array $values = [], $flush = true): BaseEntity
    {
        if (!empty($values)) {
            //Map data to entity
            $entity->fromArray($this->prepareAttributes($entity, $values));
        }

        if (!self::exist($entity)) {
            $entity->setCreated(new \DateTime());

            if ($entity->isIdSet()) {
                $metadata = $this->entityManager->getClassMetaData($entity->getClassName());
                $metadata->setIdGenerator(new AssignedGenerator);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            }

            $this->entityManager
                ->merge($entity);
        }

        if ($flush) {
            $this->flush();
        }

        return $entity;
    }

    /**
     * Method will merge entity to DB when same entity doesnt exist. Existing entity will be checked by given keys
     * @param BaseEntity $entity
     * @param array $values
     * @param array $keys
     * @param boolean $flush
     * @return BaseEntity
     */
    public function mergeIfNotExistByKeys(BaseEntity $entity, array $values = [], array $keys, $flush = true): BaseEntity
    {
        if (!empty($values)) {
            //Map data to entity
            $entity->fromArray($this->prepareAttributes($entity, $values));
        }

        $foundEntity = $this->entityManager
            ->getRepository($entity->getClassName())
            ->findOneByKeys($entity, $keys);

        if (!$foundEntity) {
            $entity->setCreated(new \DateTime());

            if ($entity->isIdSet()) {
                $metadata = $this->entityManager->getClassMetaData($entity->getClassName());
                $metadata->setIdGenerator(new AssignedGenerator);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            }

            $entity = $this->entityManager
                ->merge($entity);

            if ($flush) {
                $this->flush();
            }

            return $entity;
        }

        return $foundEntity;
    }

    /**
     * Method will check if entity exists in DB by Its ID
     * @param BaseEntity $entity
     * @return bool
     */
    public function exist(BaseEntity $entity): bool
    {
        return $this->entityManager
            ->getRepository($entity->getClassName())
            ->find($entity) ? true : false;
    }

    /**
     * Method will check if entity exists in DB by given keys
     * @param BaseEntity $entity
     * @param array $keys
     * @return BaseEntity
     */
    public function existByKeys(BaseEntity $entity, array $keys): BaseEntity
    {
        $foundEntity = $this->entityManager
            ->getRepository($entity->getClassName())
            ->findOneByKeys($entity, $keys);

        return $foundEntity ? $foundEntity : $entity;
    }


    /**
     * Method will delete entity to DB
     * @param BaseEntity $entity
     * @param boolean $flush
     * @return BaseEntity
     */
    public function delete(BaseEntity $entity, $flush = true): BaseEntity
    {
        $this->entityManager
            ->remove($entity);

        if ($flush) {
            $this->flush();
        }

        return $entity;
    }

    /**
     * Method will mark entity as deleted but will not delete it
     * @param BaseEntity $entity
     * @param boolean $flush
     * @return BaseEntity
     */
    public function deleteSafe(BaseEntity $entity, $flush = true): BaseEntity
    {
        $entity->setSafedelete(1);
        $entity->setDeleted(new \DateTime());
        $result = $this->update($entity);

        if ($flush) {
            $this->flush();
        }

        return $result;
    }

    /**
     * Method will update entity
     * @param BaseEntity $entity
     * @param array $values
     * @param boolean $flush
     * @return BaseEntity
     */
    public function update(BaseEntity $entity, array $values = [], $flush = true): BaseEntity
    {
        if (!empty($values)) {
            //Map data to entity
            $entity->fromArray($this->prepareAttributes($entity, $values));
        }

        $entity->setModified(new \DateTime());
        $this->entityManager->merge($entity);

        if ($flush) {
            $this->flush();
        }

        return $entity;
    }

    /**
     * Method will count number of entities by ID
     * @param BaseEntity $entity
     * @return int
     */
    public function count(BaseEntity $entity): int
    {
        return (int)$this->entityManager
            ->getRepository($entity->getClassName())
            ->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Method will flush changes
     */
    public function flush()
    {
        $this->entityManager->flush();
        /*
        try {
        }
        catch (UniqueConstraintViolationException $e) {
            //Skipping duplicates
        }
        */
    }

    /**
     * Prepare attributes for BaseEntity
     * @param BaseEntity $entity
     * @param array $attributes
     * @return mixed
     */
    public function prepareAttributes(BaseEntity $entity, array $attributes)
    {
        foreach ($attributes as $fieldName => &$fieldValue) {
            if (!$this->entityManager->getClassMetadata($entity->getClassName())->hasAssociation($fieldName)) {
                continue;
            }

            $association = $this->entityManager->getClassMetadata($entity->getClassName())
                ->getAssociationMapping($fieldName);

            if (is_null($fieldValue)) {
                continue;
            }

            $fieldValue = $this->entityManager->getReference($association['targetEntity'], $fieldValue);

            unset($fieldValue);
        }

        return $attributes;
    }
}
