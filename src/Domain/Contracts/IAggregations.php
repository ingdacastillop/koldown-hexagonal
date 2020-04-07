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
     * @param string $keyAggregation
     * @param string $class
     * @param bool $mappable 
     * @return IAggregations
     */
    public function hasOne(string $keyAggregation, string $class, bool $mappable = true): IAggregations;
    
    /**
     * 
     * @param string $keyAggregation
     * @param string $class
     * @param bool $mappable 
     * @return IAggregations
     */
    public function hasMany(string $keyAggregation, string $class, bool $mappable = true): IAggregations;
    
    /**
     * 
     * @param string $keyAggregation
     * @param string $class
     * @param bool $mappable 
     * @return IAggregations
     */
    public function composedBy(string $keyAggregation, string $class, bool $mappable = true): IAggregations;
    
    /**
     * 
     * @param string $keyAggregation
     * @param string $class
     * @param string|null $column
     * @param bool $mappable
     * @return IAggregations
     */
    public function belongTo(string $keyAggregation, string $class, ?string $column = null, bool $mappable = true): IAggregations;
    
    /**
     * 
     * @param string $keyAggregation
     * @param string $class
     * @param bool $mappable 
     * @return IAggregations
     */
    public function containTo(string $keyAggregation, string $class, bool $mappable = true): IAggregations;

    /**
     * 
     * @return IAggregationsKeys
     */
    public function keys(): IAggregationsKeys;
    
    /**
     * 
     * @return array
     */
    public function forCascade(): array;
    
    /**
     * 
     * @return array
     */
    public function forHidration(): array;
    
    /**
     * 
     * @return array
     */
    public function forBelong(): array;
    
    /**
     * 
     * @return array
     */
    public function forMappable(): array;
}