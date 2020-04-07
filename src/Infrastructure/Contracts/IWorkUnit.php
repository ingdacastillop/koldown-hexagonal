<?php

namespace Koldown\Hexagonal\Infrastructure\Contracts;

use Koldown\Hexagonal\Domain\Contracts\IRepository;
use Koldown\Hexagonal\Domain\Contracts\IEntity;
use Koldown\Hexagonal\Domain\Contracts\IEntityCollection;

interface IWorkUnit {
    
    // Métodos de la interfaz IWorkUnit
    
    /**
     * 
     * @param string $context
     * @return void
     */
    public function setContext(string $context): void;
    
    /**
     * 
     * @return string|null
     */
    public function getContext(): ?string;
    
    /**
     * 
     * @return int
     */
    public function getNow(): int;

    /**
     * 
     * @param string $classEntity
     * @return IRepository
     */
    public function getRepository(string $classEntity): ?IRepository;
    
    /**
     * 
     * @param IEntity $entity
     * @return void
     */
    public function addEntity(IEntity $entity): void;
    
    /**
     * 
     * @param array $entities
     * @return void
     */
    public function addEntities(array $entities): void;
    
    /**
     * 
     * @param IEntity $entity
     * @return void
     */
    public function persist(IEntity $entity): void;
    
    /**
     * 
     * @param IEntityCollection $collection
     * @return void
     */
    public function persists(IEntityCollection $collection): void;
    
    /**
     * 
     * @param IEntity $entity
     * @return void
     */
    public function safeguard(IEntity $entity): void;
    
    /**
     * 
     * @param IEntityCollection $collection
     * @return void
     */
    public function safeguards(IEntityCollection $collection): void;

    /**
     * 
     * @param IEntity $entity
     * @return void
     */
    public function destroy(IEntity $entity): void;

    /**
     * 
     * @param IEntityCollection $collection
     * @return void
     */
    public function destroys(IEntityCollection $collection): void;

    /**
     * 
     * @return void
     */
    public function transaction(): void;

    /**
     * 
     * @return void
     */
    public function commit(): void;
    
    /**
     * 
     * @return void
     */
    public function rollback(): void;
}