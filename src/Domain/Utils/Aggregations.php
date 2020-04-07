<?php

namespace Koldown\Hexagonal\Domain\Utils;

use Closure;

use Koldown\Hexagonal\Domain\Contracts\IAggregationsKeys;
use Koldown\Hexagonal\Domain\Contracts\IAggregation;
use Koldown\Hexagonal\Domain\Contracts\IAggregations;

class Aggregations implements IAggregations {
    
    // Atributos de la clase Aggregations
    
    /**
     *
     * @var array 
     */
    private $aggregations = [];
    
    // Métodos de la clase Aggregations
    
    /**
     * 
     * @param Closure $closure
     * @return array
     */
    protected function forProcess(Closure $closure): array {
        $cascades = []; // Contenedor de relaciones para gestion de datos
        
        foreach ($this->aggregations as $key => $value) {
            if ($closure($value)) {
                $cascades[$key] = $value;
            }
        } // Agregando relaciones para gestion de datos
        
        return $cascades; // Retornando relaciones para gestion de datos
    }
    
    // Métodos sobrescritos de la clase IAggregations
    
    public function set(string $key, IAggregation $aggregation): void {
        $this->aggregations[$key] = $aggregation; 
    }
    
    public function exists(string $key): bool {
        return isset($this->aggregations[$key]);
    }
    
    public function get(string $key): ?IAggregation {
        return (!$this->exists($key)) ? null : $this->aggregations[$key];
    }
    
    public function hasOne(string $keyAggregation, string $class, bool $mappable = true): IAggregations {
        $this->set($keyAggregation, new HasOne($class, $mappable)); return $this;
    }
    
    public function hasMany(string $keyAggregation, string $class, bool $mappable = true): IAggregations {
        $this->set($keyAggregation, new HasMany($class, $mappable)); return $this;
    }
    
    public function composedBy(string $keyAggregation, string $class, bool $mappable = true): IAggregations {
        $this->set($keyAggregation, new ComposedBy($class, $mappable)); return $this;
    }
    
    public function belongTo(string $keyAggregation, string $class, ?string $column = null, bool $mappable = true): IAggregations {
        if (is_null($column)) {
            $column = "{$keyAggregation}_id"; // Redefiniendo valor de clave de la columna 
        }
        
        $this->set($keyAggregation, new BelongTo($class, $column, $mappable)); return $this;
    }
    
    public function containTo(string $keyAggregation, string $class, bool $mappable = true): IAggregations {
        $this->set($keyAggregation, new ContainTo($class, $mappable)); return $this;
    }
    
    public function keys(): IAggregationsKeys {
        return AggregationsKeys::getInstance()->setAggregations($this->aggregations);
    }
    
    public function forCascade(): array {
        return $this->forProcess(function (IAggregation $aggregation) { return $aggregation->isCascade(); });
    }
    
    public function forHidration(): array {
        return $this->forProcess(function (IAggregation $aggregation) { return $aggregation->isHidration(); });
    }
    
    public function forBelong(): array {
        return $this->forProcess(function (IAggregation $aggregation) { return $aggregation->isBelong(); });
    }
    
    public function forMappable(): array {
        return $this->forProcess(function (IAggregation $aggregation) { return $aggregation->isMappable(); });
    }
}