<?php

namespace Koldown\Hexagonal\Infrastructure\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface IModelBuilder {
    
    // Métodos de la interfaz IModelBuilder

    /**
     * 
     * @param array $additionals
     * @return Builder
     */
    public function columns(array $additionals = []): Builder;
    
    /**
     * 
     * @param array $additionals
     * @return Builder
     */
    public function aggregations(array $additionals = []): Builder;
}