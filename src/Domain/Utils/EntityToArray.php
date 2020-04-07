<?php

namespace Koldown\Hexagonal\Domain\Utils;

use ReflectionClass;
use ReflectionProperty;

use Koldown\Hexagonal\Domain\Contracts\IEntity;
use Koldown\Hexagonal\Domain\Contracts\IEntityCollection;

use Koldown\Utils\Str;

class EntityToArray {
    
    // Atributos de la clase EntityToArray
    
    /**
     *
     * @var ReflectionClass 
     */
    private $reflection;
    
    // Métodos de la clase EntityToArray
    
    /**
     * 
     * @param IEntity $entity
     * @return array
     */
    public function execute(IEntity $entity, $discards = []): array {
        $this->reflection = new ReflectionClass($entity);
        $result           = []; // Array de entidad
        
        foreach ($this->reflection->getProperties() as $property) {
            if (!in_array($property->getName(), $discards)) { 
                $value = $this->getValueKeyEntity($property, $entity);

                if (!is_null($value)) {
                    $result[$property->getName()] = $value; // Estableciendo
                }
            } 
        }
        
        return $result; // Retorna array generado de la entidad
    }

    /**
     * 
     * @param ReflectionProperty $property
     * @param IEntity $entity
     * @return mixed
     */
    private function getValueKeyEntity(ReflectionProperty $property, IEntity $entity) {
        $value = ($property->isPublic()) ? $property->getValue($entity) :
            $this->getValueMethodEntity($property, $entity);

        if ($value instanceof IEntity) {
            return $value->jsonSerialize();
        } else if ($value instanceof IEntityCollection) {
            return $value->jsonSerialize();
        }
        
        return $value; // Retornando valor de la property
    }

    /**
     * 
     * @param ReflectionProperty $property
     * @param IEntity $entity
     * @return mixed
     */
    private function getValueMethodEntity(ReflectionProperty $property, IEntity $entity) {        
        $getter = Str::getInstance()->getCamelCase()->getter($property->getName());
            
        if ($this->reflection->hasMethod($getter)) {
            return $this->getValueMethod($getter, $entity); // Método getter
        }
        
        $ister = Str::getInstance()->getCamelCase()->ister($property->getName());
            
        if ($this->reflection->hasMethod($ister)) {
            return $this->getValueMethod($ister, $entity);  // Método ister
        }
    }
    
    /**
     * 
     * @param string $method
     * @param IEntity $entity
     * @return mixed
     */
    private function getValueMethod(string $method, IEntity $entity) {
        $accessor = $this->reflection->getMethod($method); // Método de la entidad
            
        return (!$accessor->isPublic()) ? null : $accessor->invoke($entity);
    }
}