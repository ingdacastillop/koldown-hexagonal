<?php

namespace Koldown\Hexagonal\Domain\Utils;

use ReflectionClass;

use Koldown\Hexagonal\Domain\Contracts\IEntity;
use Koldown\Hexagonal\Domain\Contracts\IAggregationMapper;

class AggregationMapperCascade implements IAggregationMapper {
    
    // Atributos de la clase AggregationMapperCascade
    
    /**
     *
     * @var ReflectionClass 
     */
    private $reflection;
    
    /**
     *
     * @var array 
     */
    private $aggregations = [];
    
    // Métodos de la clase AggregationMapperCascade
    
    public function toEntity(IEntity $entity): array {
        $this->aggregations = []; // Listado de agregaciones
        
        $keys = $entity->getAggregations()->getCascadeKeys();
        
        $this->reflection = new ReflectionClass($entity);
        
        foreach ($keys as $key) {
            $this->getValueKeyEntity($key, $entity);
        } // Recorriendo claves de cascada de entity
        
        return $this->aggregations; // Retornando agregaciones
    }
    
    /**
     * 
     * @param string $key
     * @param IEntity $entity
     * @return void
     */
    private function getValueKeyEntity(string $key, IEntity $entity): void {
        if ($this->reflection->hasProperty($key)) {
            $accessor = $this->reflection->getProperty($key);

            if ($accessor->isPublic()) {
                $this->addValueAggregations($accessor->getValue($entity)); return;
            }
        } // Verificando si se puede asignar por propiedad
        
        $this->getValueMethodEntidad($key, $entity); // Asignación por método
    }
    
    /**
     * 
     * @param string $key
     * @param IEntity $entity
     * @return void
     */
    private function getValueMethodEntidad(string $key, IEntity $entity): void {        
        $getter = "get{$this->getCamelCaseName($key)}"; // Método getter
            
        if ($this->reflection->hasMethod($getter)) {
            $accessor = $this->reflection->getMethod($getter);
            
            if ($accessor->isPublic()) {
                $this->addValueAggregations($accessor->invoke($entity)); 
            }
        }
    }
    
    /**
     * 
     * @param mixed $value
     * @return void
     */
    private function addValueAggregations($value): void {
        if (!is_null($value)) {
            array_push($this->aggregations, $value);
        } // Se debe agregar en lista de agregaciones para registro
    }

    /**
     * 
     * @param string $property
     * @return string
     */
    private function getCamelCaseName(string $property): string {
        return str_replace(" ", "", ucwords(str_replace(array("_", "-"), " ", $property)));
    }
}