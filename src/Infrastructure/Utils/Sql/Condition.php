<?php

namespace Koldown\Hexagonal\Infrastructure\Utils\Sql;

use Illuminate\Database\Eloquent\Builder;

use Koldown\Hexagonal\Infrastructure\Contracts\Sql\IPredicate;

class Condition implements IPredicate {
    
    // Atributos de la clase Condition
    
    /**
     *
     * @var string 
     */
    private $column;
    
    /**
     *
     * @var string 
     */
    private $operator;
    
    /**
     *
     * @var mixed 
     */
    private $value;
    
    /**
     *
     * @var bool 
     */
    private $or;
    
    // Constructor de la clase Condition
    
    /**
     * 
     * @param string $column
     * @param string|null $operator
     * @param mixed $value
     * @param bool $or
     */
    public function __construct(string $column, ?string $operator, $value, bool $or = false) {
        $this->setColumn($column); 
        $this->setOperator($operator); 
        $this->setValue($value);
        $this->setOr($or);
    }
    
    // Métodos sobrescritos de la interfaz ICondition
    
    public function setColumn(string $column): void {
        $this->column = $column;
    }
    
    public function getColumn(): ?string {
        return $this->column;
    }
    
    public function setOperator(?string $operator): void {
        $this->operator = $operator;
    }
    
    public function getOperator(): ?string {
        return $this->operator;
    }
    
    public function setValue($value): void {
        $this->value = $value;
    }
    
    public function getValue() {
        return $this->value;
    }
    
    public function setOr(bool $or): void {
        $this->or = $or;
    }
    
    public function isOr(): bool {
        return $this->or;
    }

    public function flush(Builder $builder): void {
        if ($this->isOr()) {
            $builder->orWhere($this->getColumn(), $this->getOperator(), $this->getValue());
        } else {
            $builder->where($this->getColumn(), $this->getOperator(), $this->getValue());
        }
    }
}