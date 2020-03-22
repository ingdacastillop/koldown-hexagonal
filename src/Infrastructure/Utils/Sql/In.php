<?php

namespace Koldown\Hexagonal\Infrastructure\Utils\Sql;

use Illuminate\Database\Eloquent\Builder;

class In extends Condition {
    
    // Constructor de la clase In
    
    /**
     * 
     * @param string $column
     * @param array $value
     * @param bool $or
     */
    public function __construct(string $column, array $value, bool $or = false) {
        parent::__construct($column, null, $value, $or);
    }
    
    // Métodos sobrescritos de la clase Condition
    
    public function flush(Builder $builder): void {
        if ($this->isOr()) {
            $builder->orWhereIn($this->getColumn(), $this->getValue());
        } else {
            $builder->whereIn($this->getColumn(), $this->getValue());
        }
    }
}