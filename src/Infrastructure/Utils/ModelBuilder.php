<?php

namespace Koldown\Hexagonal\Infrastructure\Utils;

use Illuminate\Database\Eloquent\Builder;

use Koldown\Hexagonal\Infrastructure\Contracts\IModelBuilder;

class ModelBuilder extends Builder implements IModelBuilder {
    
    // Métodos sobrescritos de la interfaz IModelBuilder
    
    public function columns(array $additionals = []): Builder {
        $columns = []; // Relaciones del model del builder
        
        if (!is_null($this->getModel())) {
            $columns = $this->getModel()->getColumns();
        } // Builder conoce el modelo de gestión
        
        $this->query->select(array_merge($columns, $additionals)); 
        
        return $this; // Retornando instancia como interfaz fluida
    }
    
    public function aggregations(array $additionals = []): Builder {
        $relations = []; // Relaciones del model del builder
        
        if (!is_null($this->getModel())) {
            $relations = $this->getModel()->getAggregations();
        } // Builder conoce el modelo de gestión
        
        return $this->with(array_merge($relations, $additionals));
    }
}