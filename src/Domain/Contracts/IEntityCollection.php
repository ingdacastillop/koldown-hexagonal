<?php

namespace Koldown\Hexagonal\Domain\Contracts;

use IteratorAggregate;
use Countable;
use JsonSerializable;

interface IEntityCollection extends IteratorAggregate, Countable, JsonSerializable {
    
    // Métodos de la interfaz IEntityCollection
    
    /**
     * 
     * @param IEntity $entity
     * @return void
     */
    public function add(IEntity $entity): void;
    
    /**
     * 
     * @param int $index
     * @return IEntity|null
     */
    public function get(int $index): ?IEntity;

    /**
     * 
     * @return void
     */
    public function clear(): void;
    
    /**
     * 
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * @return array
     */
    public function toArray(): array;
}