<?php

namespace Koldown\Hexagonal\Domain\Contracts;

use JsonSerializable;
use Koldown\Hexagonal\Domain\Contracts\IAggregations;

interface IEntity extends JsonSerializable {
    
    // Métodos de la interfaz IEntityCollection
    
    /**
     * 
     * @param int $primaryKey
     * @return void
     */
    public function setPrimaryKey(int $primaryKey): void;
    
    /**
     * 
     * @return int|null
     */
    public function getPrimaryKey(): ?int;
    
    /**
     * 
     * @param int $parentKey
     * @return void
     */
    public function setParentKey(int $parentKey): void;
    
    /**
     * 
     * @return int|null
     */
    public function getParentKey(): ?int;
    
    /**
     * 
     * @return string
     */
    public function getTable(): string;

    /**
     * 
     * @return IAggregations
     */
    public function getAggregations(): IAggregations;

    /**
     * 
     * @param array $discards
     * @return array
     */
    public function toArray(array $discards = []): array;
}