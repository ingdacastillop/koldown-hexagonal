<?php

namespace Koldown\Hexagonal\Infrastructure\Contracts;

use Koldown\Hexagonal\Domain\Contracts\IRepository;
use Koldown\Hexagonal\Domain\Contracts\IEntity;

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
    public function setEntity(IEntity $entity): void;
    
    /**
     * 
     * @param array $entities
     * @return void
     */
    public function setEntities(array $entities): void;
    
    /**
     * 
     * @param IEntity $entity
     * @return void
     */
    public function persist(IEntity $entity): void;
    
    /**
     * 
     * @param IEntity $entity
     * @return void
     */
    public function safeguard(IEntity $entity): void;

    /**
     * 
     * @param IEntity $entity
     * @return void
     */
    public function destroy(IEntity $entity): void;

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