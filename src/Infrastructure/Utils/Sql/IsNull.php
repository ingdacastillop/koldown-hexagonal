<?php

namespace Koldown\Hexagonal\Infrastructure\Utils\Sql;

use Illuminate\Database\Eloquent\Builder;

class IsNull extends Condition {
    
    // Constructor de la clase IsNull
    
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
            $builder->orWhereNull($this->getColumn());
        } else {
            $builder->whereNull($this->getColumn());
        }
    }
}