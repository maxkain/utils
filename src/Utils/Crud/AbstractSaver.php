<?php

namespace App\Utils\Crud;

use App\Utils\DtoMapper\DtoMapper;
use App\Utils\Service\ServiceSubscriberTrait;
use App\Utils\Service\ServiceSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractSaver implements CreatorInterface, UpdaterInterface, ServiceSubscriberInterface
{
    use ServiceSubscriberTrait;

    public static function getSubscribedServices(): array
    {
        return [
            DtoMapper::class,
            ManagerRegistry::class
        ];
    }

    abstract public function getEntityClass(): string;
    abstract public function getDtoClass(): string;

    protected function mapFromDto(mixed $dto, mixed $entity): object
    {
        $this->getDtoMapper()->mapFromDto($dto, $entity);

        return $dto;
    }

    protected function mapToDto(mixed $entity, mixed $dto): object
    {
        $this->getDtoMapper()->mapToDto($entity, $dto);

        return $dto;
    }

    public function createEntityFromDto(object $dto, bool $flush = true): object
    {
        $entity = $this->createEntity();
        $this->mapFromDto($dto, $entity);
        $this->checkerCreate($entity);

        return $this->saveEntity($entity, $flush);
    }


    public function updateEntityFromDto(object $dto, object $entity, bool $flush = true): object
    {
        $this->mapFromDto($dto, $entity);

        return $this->saveEntity($entity, $flush);
    }

    protected function saveEntity(mixed $entity, bool $flush = true): object
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $entity;
    }

    public function createDtoFromEntity(object $entity): object
    {
        $dto = $this->createDto();
        $this->mapToDto($entity, $dto);

        return $dto;
    }

    public function findEntity(mixed $id): ?object
    {
        return $this->getRepository()->find($id);
    }

    private function getRepository(): EntityRepository
    {
        return $this->getEntityManager()->getRepository($this->getEntityClass());
    }

    private function getEntityManager(): EntityManagerInterface
    {
        $managerRegistry = $this->container->get(ManagerRegistry::class);

        return $managerRegistry->getManagerForClass($this->getEntityClass());
    }

    protected function getDtoMapper(): DtoMapper
    {
        return $this->container->get(DtoMapper::class);
    }

    protected function createEntity(): object
    {
        return new ($this->getEntityClass());
    }

    public function createDto(): object
    {
        return new ($this->getDtoClass());
    }
}
