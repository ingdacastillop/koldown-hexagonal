<?php

namespace Koldown\Hexagonal\Domain\Contracts;

interface IAggregationArray {
    
    // Métodos de la interfaz IAggregationArray
    
    /**
     * 
     * @param IEntity $entity
     * @return array
     */
    public function ofEntity(IEntity $entity): array;
}