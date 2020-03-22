<?php

namespace Koldown\Hexagonal\Infrastructure\Contracts\Sql;

interface IPredicate extends IClause {
    
    // Métodos de la interfaz IPredicate
    
    /**
     * 
     * @param string $column
     * @return void
     */
    public function setColumn(string $column): void;
    
    /**
     * 
     * @return string|null
     */
    public function getColumn(): ?string;
    
    /**
     * 
     * @param string|null $operator
     * @return void
     */
    public function setOperator(?string $operator): void;
    
    /**
     * 
     * @return string|null
     */
    public function getOperator(): ?string;
    
    /**
     * 
     * @param mixed $value
     * @return void
     */
    public function setValue($value): void;
    
    /**
     * 
     * @return mixed
     */
    public function getValue();
    
    /**
     * 
     * @param bool $or
     * @return void
     */
    public function setOr(bool $or): void;
    
    /**
     * 
     * @return bool|null
     */
    public function isOr(): bool;
}