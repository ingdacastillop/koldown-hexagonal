<?php

namespace Koldown\Hexagonal\Infrastructure\Utils\Sql;

use Illuminate\Database\Eloquent\Builder;

use Koldown\Hexagonal\Infrastructure\Contracts\Sql\IClause;

class Offset implements IClause {
    
    // Atributos de la clase Offset
    
    /**
     *
     * @var int 
     */
    private $count;
    
    // Constructor de la clase Offset
    
    /**
     * 
     * @param int $count
     */
    public function __construct(int $count) {
        $this->setCount($count);
    }
    
    // Métodos de la clase Offset
    
    /**
     * 
     * @param int $count
     * @return void
     */
    public function setCount(int $count): void {
        $this->count = $count;
    }
    
    /**
     * 
     * @return int
     */
    public function getCount() {
        return $this->count;
    }

    // Métodos sobrescritos de la interfaz IClause
    
    public function flush(Builder $builder): void {
        $builder->offset($this->getCount());
    }
}