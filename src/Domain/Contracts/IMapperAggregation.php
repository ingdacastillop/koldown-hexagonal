<?php

namespace Koldown\Hexagonal\Domain\Contracts;

interface IAggregationMapper {
    
    // Métodos de la interfaz IAggregationMapper
    
    /**
     * 
     * @param IEntity $entity
     * @return array
     */
    public function toEntity(IEntity $entity): array;
}