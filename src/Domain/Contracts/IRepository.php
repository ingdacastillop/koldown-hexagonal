<?php

namespace Koldown\Hexagonal\Domain\Contracts;

interface IRepository {
    
    // Métodos de la interfaz IRepository
    
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
     * @return IEntity|null
     */
    public function getEntity(): ?IEntity;

    /**
     * 
     * @param IEntity $entity
     * @return void
     */
    public function insert(IEntity $entity): void;
    
    /**
     * 
     * @return IEntityCollection
     */
    public function findAll(): IEntityCollection;

    /**
     * 
     * @param mixed $id
     * @return IEntity|null
     */
    public function find($id): ?IEntity;
    
    /**
     * 
     * @param array $aggregations
     * @return IEntityCollection
     */
    public function fetchAll(array $aggregations = null): IEntityCollection;

    /**
     * 
     * @param mixed $id
     * @param array $aggregations
     * @return IEntity|null
     */
    public function fetch($id, array $aggregations = null): ?IEntity;
    
    /**
     * 
     * @return IEntityCollection
     */
    public function resources(): IEntityCollection;
    
    /**
     * 
     * @param mixed $id
     * @param array $data
     * @return void
     */
    public function update($id, array $data): void;
    
    /**
     * 
     * @param IEntity $entity
     * @return void
     */
    public function save(IEntity $entity): void;
    
    /**
     * 
     * @param IEntity $entity
     * @return void
     */
    public function delete(IEntity $entity): void;
}