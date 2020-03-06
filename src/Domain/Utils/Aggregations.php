<?php

namespace Koldown\Hexagonal\Domain\Utils;

use Koldown\Hexagonal\Domain\Contracts\IAggregation;
use Koldown\Hexagonal\Domain\Contracts\IAggregations;

class Aggregations implements IAggregations {
    
    // Atributos de la clase Aggregations
    
    /**
     *
     * @var array 
     */
    private $aggregations = [];
    
    // Constantes de la clase Aggregations
    
    const HAS_ONE    = 101;
    
    const HAS_MANY   = 102;
    
    const BELONG_TO  = 201;
    
    const CONTAIN_TO = 202;
    
    // Métodos de la clase Aggregations
    
    public function set(string $key, IAggregation $aggregation): void {
        $this->aggregations[$key] = $aggregation; 
    }
    
    public function exists(string $key): bool {
        return isset($this->aggregations[$key]);
    }
    
    public function get(string $key): ?IAggregation {
        return (!$this->exists($key)) ? null : $this->aggregations[$key];
    }
    
    public function attach(string $key, string $class, int $type, bool $composition = true): IAggregations {
        switch ($type) {
            case (self::HAS_ONE):
                $this->set($key, new Aggregation($class, false, true, false, $composition));
            break;
                
            case (self::HAS_MANY):
                $this->set($key, new Aggregation($class, true, true, false, $composition));
            break;
        
            case (self::BELONG_TO):
                $this->set($key, new Aggregation($class, false, false, true, $composition));
            break;
        
            case (self::CONTAIN_TO):
                $this->set($key, new Aggregation($class, false, false, false, $composition));
            break;
        }
        
        return $this; // Retornando instancia como variable para recursividad
    }
    
    public function hasOne(string $key, string $class, bool $composition = true): IAggregations {
        return $this->attach($key, $class, self::HAS_ONE, $composition);
    }
    
    public function hasMany(string $key, string $class, bool $composition = true): IAggregations {
        return $this->attach($key, $class, self::HAS_MANY, $composition);
    }
    
    public function belongTo(string $key, string $class, bool $composition = true): IAggregations {
        return $this->attach($key, $class, self::BELONG_TO, $composition);
    }
    
    public function containTo(string $key, string $class, bool $composition = true): IAggregations {
        return $this->attach($key, $class, self::CONTAIN_TO, $composition);
    }
    
    public function getKeys(): array {
        return array_keys($this->aggregations);
    }
    
    public function getHidration(): array {
        $hidrations = []; // Contenedor de relaciones de hidratación
        
        foreach ($this->aggregations as $key => $value) {
            if ($value->isHidration()) {
                $hidrations[$key] = $value;
            }
        } // Agregando relaciones de hidratación
        
        return $hidrations; // Retornando relaciones de hidratación
    }
    
    public function getHidrationKeys(): array {
        $hidrations = []; // Contenedor de claves de hidrataciones
        
        foreach ($this->aggregations as $key => $value) {
            if ($value->isHidration()) {
                array_push($hidrations, $key);
            }
        } // Agregando claves de las hidrataciones
        
        return $hidrations; // Retornando claves de hidrataciones
    }
    
    public function getCascade(): array {
        $cascades = []; // Contenedor de relaciones en cascada
        
        foreach ($this->aggregations as $key => $value) {
            if ($value->isCascade()) {
                $cascades[$key] = $value;
            }
        } // Agregando relaciones en cascada
        
        return $cascades; // Retornando relaciones en cascada
    }
    
    public function getCascadeKeys(): array {
        $cascades = []; // Contenedor de claves de cascada
        
        foreach ($this->aggregations as $key => $value) {
            if ($value->isCascade()) {
                array_push($cascades, $key);
            }
        } // Agregando claves de las cascada
        
        return $cascades; // Retornando claves de cascada
    }
    
    public function getComposition(): array {
        $compositions = []; // Contenedor de relaciones de composición
        
        foreach ($this->aggregations as $key => $value) {
            if ($value->isComposition()) {
                $compositions[$key] = $value;
            }
        } // Agregando relaciones de composición
        
        return $compositions; // Retornando relaciones de composición
    }
    
    public function getCompositionKeys(): array {
        $aggregations = []; // Contenedor de claves de agregaciones
        
        foreach ($this->aggregations as $key => $value) {
            if ($value->isComposition()) {
                array_push($aggregations, $key);
            }
        } // Agregando claves de las agregaciones
        
        return $aggregations; // Retornando claves de agregaciones
    }
}