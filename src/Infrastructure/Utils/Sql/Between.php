<?php

namespace Koldown\Hexagonal\Infrastructure\Utils\Sql;

use Illuminate\Database\Eloquent\Builder;

class Between extends Condition {
    
    // Constructor de la clase Between
    
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
            $builder->orWhereBetween($this->getColumn(), $this->getValue());
        } else {
            $builder->whereBetween($this->getColumn(), $this->getValue());
        }
    }
}