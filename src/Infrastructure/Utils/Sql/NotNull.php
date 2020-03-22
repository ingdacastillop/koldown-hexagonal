<?php

namespace Koldown\Hexagonal\Infrastructure\Utils\Sql;

use Illuminate\Database\Eloquent\Builder;

class NotNull extends Condition {
    
    // Constructor de la clase NotNull
    
    /**
     * 
     * @param string $column
     * @param bool $or
     */
    public function __construct(string $column, bool $or = false) {
        parent::__construct($column, null, null, $or);
    }
    
    // Métodos sobrescritos de la clase Condition
    
    public function flush(Builder $builder): void {
        if ($this->isOr()) {
            $builder->orWhereNotNull($this->getColumn());
        } else {
            $builder->whereNotNull($this->getColumn());
        }
    }
}