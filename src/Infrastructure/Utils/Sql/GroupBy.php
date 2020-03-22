<?php

namespace Koldown\Hexagonal\Infrastructure\Utils\Sql;

use Illuminate\Database\Eloquent\Builder;

use Koldown\Hexagonal\Infrastructure\Contracts\Sql\IClause;

class GroupBy implements IClause {
    
    // Atributos de la clase GroupBy
    
    /**
     *
     * @var array 
     */
    private $columns;
    
    // Métodos de la clase GroupBy
    
    /**
     * 
     * @param array $columns
     * @return void
     */
    public function setColumns(...$columns): void {
        $this->columns = $columns;
    }
    
    /**
     * 
     * @return array|null
     */
    public function getColumns() {
        return $this->columns;
    }

    // Métodos sobrescritos de la interfaz IClause
    
    public function flush(Builder $builder): void {
        $builder->groupBy($this->getColumns());
    }
}