<?php

namespace Koldown\Hexagonal\Domain\Utils;

use ReflectionClass;

use Koldown\Hexagonal\Domain\Contracts\IEntity;
use Koldown\Hexagonal\Domain\Contracts\IAggregationArray;

use Koldown\Utils\Str;

class AggregationCascade implements IAggregationArray {
    
    // Atributos de la clase AggregationCascade
    
    /**
     *
     * @var ReflectionClass 
     */
    private $reflection;
    
    // Métodos de la clase AggregationCascade
    
    public function ofEntity(IEntity $entity): array {
        $aggregations = []; // Listado de agregaciones de la entidad
        
        $keys = $entity->getAggregations()->keys()->cascade();
        
        $this->reflection = new ReflectionClass($entity);
        
        foreach ($keys as $key) {
            $value = $this->getValueKeyEntity($key, $entity); // Valor
            
            if (!is_null($value)) {
                array_push($aggregations, $value);
            } // Agregando resultado en lista para registro
        } // Recorriendo claves de cascada de entity
        
        return $aggregations; // Retornando agregaciones de la entidad
    }
    
    /**
     * 
     * @param string $key
     * @param IEntity $entity
     * @return mixed
     */
    private function getValueKeyEntity(string $key, IEntity $entity) {
        if ($this->reflection->hasProperty($key)) {
            $accessor = $this->reflection->getProperty($key);

            if ($accessor->isPublic()) {
                return $accessor->getValue($entity);
            }
        } // Verificando si se puede obtener por propiedad
        
        return $this->getValueMethodEntity($key, $entity); // Método
    }
    
    /**
     * 
     * @param string $key
     * @param IEntity $entity
     * @return mixed
     */
    private function getValueMethodEntity(string $key, IEntity $entity) {        
        $getter = Str::getInstance()->getCamelCase()->getter($key);
            
        if ($this->reflection->hasMethod($getter)) {
            $accessor = $this->reflection->getMethod($getter);
            
            if ($accessor->isPublic()) {
                return $accessor->invoke($entity); 
            }
        }
        
        return null; // No se puede obtener valor de clave en la Entidad
    }
}