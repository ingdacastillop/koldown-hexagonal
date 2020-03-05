<?php

namespace Koldown\Hexagonal\Domain\Contracts;

use Koldown\Hexagonal\Domain\Contracts\IEntity;

interface IEntityMapper {
    
    // Métodos de la interfaz IEntityMapper
    
    /**
     * 
     * @return IEntityMapper
     */
    public function clean(): IEntityMapper;
    
    /**
     * 
     * @return array
     */
    public function getEntities(): array;

    /**
     * 
     * @param array $source
     * @param IEntity $destination
     * @return IEntity|null
     */
    public function ofArray(?array $source, IEntity $destination): ?IEntity;
}