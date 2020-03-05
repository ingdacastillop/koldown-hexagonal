<?php

namespace Koldown\Hexagonal\Domain\Contracts;

interface IAggregations {
    
    // Métodos de la interfaz IAggregations
    
    /**
     * 
     * @param string $key
     * @param IAggregation $aggregation
     * @return void
     */
    public function set(string $key, IAggregation $aggregation): void;
    
    /**
     * 
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool;
    
    /**
     * 
     * @param string $key
     * @return IAggregation|null
     */
    public function get(string $key): ?IAggregation;
    
    /**
     * 
     * @param string $key
     * @param string $class
     * @param int $type
     * @param bool $composition 
     * @return IAggregations
     */
    public function attach(string $key, string $class, int $type, bool $composition = true): IAggregations;
    
    /**
     * 
     * @param string $key
     * @param string $class
     * @param bool $composition 
     * @return IAggregations
     */
    public function hasOne(string $key, string $class, bool $composition = true): IAggregations;
    
    /**
     * 
     * @param string $key
     * @param string $class
     * @param bool $composition 
     * @return IAggregations
     */
    public function hasMany(string $key, string $class, bool $composition = true): IAggregations;
    
    /**
     * 
     * @param string $key
     * @param string $class
     * @param bool $composition 
     * @return IAggregations
     */
    public function belongTo(string $key, string $class, bool $composition = true): IAggregations;
    
    /**
     * 
     * @param string $key
     * @param string $class
     * @param bool $composition 
     * @return IAggregations
     */
    public function containTo(string $key, string $class, bool $composition = true): IAggregations;

    /**
     * 
     * @return array
     */
    public function getKeys(): array;
    
    /**
     * 
     * @return array
     */
    public function getHidration(): array;
    
    /**
     * 
     * @return array
     */
    public function getHidrationKeys(): array;
    
    /**
     * 
     * @return array
     */
    public function getCascade(): array;
    
    /**
     * 
     * @return array
     */
    public function getCascadeKeys(): array;
    
    /**
     * 
     * @return array
     */
    public function getComposition(): array;
    
    /**
     * 
     * @return array
     */
    public function getCompositionKeys(): array;
}