<?php

namespace Koldown\Hexagonal\Domain\Utils;

use ReflectionClass;
use ReflectionProperty;

use Koldown\Hexagonal\Domain\Contracts\IEntity;
use Koldown\Hexagonal\Domain\Contracts\IEntityCollection;

class EntityToArray {
    
    // Atributos de la clase EntityToArray
    
    /**
     *
     * @var EntityToArray 
     */
    private static $instance = null;
    
    /**
     *
     * @var ReflectionClass 
     */
    private $reflection;
    
    /**
     *
     * @var array 
     */
    private $result = [];
    
    // Constructor de la clase EntityToArray
    
    private function __construct() {
        
    }
    
    // Métodos estáticos de la clase EntityToArray

    /**
     * 
     * @return EntityToArray
     */
    public static function getInstance(): EntityToArray {
        if (is_null(self::$instance)) {
            self::$instance = new EntityToArray();
        } // Instanciando clase EntityToArray
        
        return self::$instance; // Retornando instancia
    }
    
    // Métodos de la clase EntityToArray
    
    /**
     * 
     * @param IEntity $entity
     * @return array
     */
    public function execute(IEntity $entity, $discards = []): array {
        $this->result     = []; // Reiniciando array de resultado
        
        $this->reflection = new ReflectionClass($entity);
        
        foreach ($this->reflection->getProperties() as $property) {
            if (!in_array($property->getName(), $discards)) { 
                $this->setValueKeyArray($property, $entity);
            } 
        }
        
        return $this->result; // Retorna resultado del mapeado
    }
    
    /**
     * 
     * @param ReflectionProperty $property
     * @param IEntity $entity
     */
    private function setValueKeyArray(ReflectionProperty $property, IEntity $entity) {
        $value = $this->getValueKeyEntity($property, $entity);

        if (!is_null($value)) {
            $this->result[$property->getName()] = $value; // Estableciendo
        }
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
        $getter = "get{$this->getCamelCaseName($property->getName())}";
            
        if ($this->reflection->hasMethod($getter)) {
            return $this->getValueMethod($getter, $entity); // Método getter
        }
        
        $ister = "is{$this->getCamelCaseName($property->getName())}";
            
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
        $accessor = $this->reflection->getMethod($method); // Método de la clase
            
        return (!$accessor->isPublic()) ? null : $accessor->invoke($entity);
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