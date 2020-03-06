<?php

namespace Koldown\Hexagonal\Infrastructure\Contracts;

interface IModelMapper {
    
    // Métodos de la interfaz IModelMapper
    
    /**
     * 
     * @param array $source
     * @param IModel $destination
     * @return IModel|null
     */
    public function ofArray(?array $source, IModel $destination): ?IModel;
    
    /**
     * 
     * @param array $source
     * @param array $conversions
     * @return array
     */
    public function getFormatArray(array $source, array $conversions): array;
}