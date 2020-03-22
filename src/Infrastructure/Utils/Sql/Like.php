<?php

namespace Koldown\Hexagonal\Infrastructure\Utils\Sql;

use Illuminate\Database\Eloquent\Builder;

class Like extends Condition {
    
    // Constructor de la clase Like
    
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
            $builder->orWhere($this->getColumn(), "LIKE", $this->getValue());
        } else {
            $builder->where($this->getColumn(), "LIKE", $this->getValue());
        }
    }
}