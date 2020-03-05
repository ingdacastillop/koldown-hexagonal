<?php

namespace Koldown\Hexagonal\Domain\Utils;

use ReflectionClass;

use Koldown\Hexagonal\Domain\Contracts\IEntity;
use Koldown\Hexagonal\Domain\Contracts\IEntityCollection;
use Koldown\Hexagonal\Domain\Contracts\IEntityMapper;

class EntityMapper implements IEntityMapper {
    
    // Atributos de la clase EntityMapper
    
    /**
     *
     * @var array 
     */
    private $entities = [];
    
    // Métodos sobrescritos de la interfaz IEntityMapper
    
    public function clean(): EntityMapper {
        $this->entities = []; return $this;
    }
    
    public function getEntities(): array {
        return $this->entities;
    }
    
    public function ofArray(?array $source, IEntity $destination): ?IEntity {
        if (is_null($source) || is_null($destination)) { 
            return null; // No se ha definido parametro correctamente
        } 
        
        $reflection = new ReflectionClass($destination);
        
        foreach ($source as $key => $value) {
            $this->setValueKeyEntity($reflection, $destination, $key, $value);
        } // Recorriendo claves y valores del origen
        
        array_push($this->entities, $destination); // Agregando
        
        return $destination; // Retornando entidad con sus atributos mapeados
    }
    
    // Métodos de la clase EntityMapper
    
    /**
     * 
     * @param IEntity $entity
     * @param string $key
     * @param type $value
     * @return void
     */
    private function setValueKeyEntity(ReflectionClass $reflection, IEntity $entity, string $key, $value): void {
        if ($reflection->hasProperty($key)) {
            $this->setValuePropertyEntity($reflection, $entity, $key, $value);
        } else {
            $this->setValueMethodEntity($reflection, $entity, $key, $value);
        }
    }
    
    /**
     * 
     * @param IEntity $entity
     * @param string $key
     * @param type $value
     * @return void
     */
    private function setValuePropertyEntity(ReflectionClass $reflection, IEntity $entity, string $key, $value): void {
        $accessor = $reflection->getProperty($key);

        if ($accessor->isPublic()) {
            $accessor->setValue($entity, $this->getValue($entity, $key, $value));
        } else {
            $this->setValueMethodEntity($reflection, $entity, $key, $value);
        }
    }
    
    /**
     * 
     * @param IEntity $entity
     * @param string $key
     * @param type $value
     */
    private function setValueMethodEntity(ReflectionClass $reflection, IEntity $entity, string $key, $value) {
        $setter = "set{$this->getCamelCaseName($key)}"; // Método
            
        if ($reflection->hasMethod($setter)) {
            $accessor = $reflection->getMethod($setter);
            
            if ($accessor->isPublic()) {
                $accessor->invoke($entity, $this->getValue($entity, $key, $value));
            } // Asignando valor de la propiedad por método
        }
    }
    
    /**
     * 
     * @param string $key
     * @return string
     */
    private function getCamelCaseName(string $key): string {
        return str_replace(" ", "", ucwords(str_replace(array("_", "-"), " ", $key)));
    }

    /**
     * 
     * @param IEntity $entity
     * @param string $key
     * @param mixed $value
     * @return mixed|null
     */
    private function getValue(IEntity $entity, string $key, $value) {
        if (is_null($value)) { 
            return null; // Valor indefinido, no se debe gestionar dato
        } 
        
        if ($entity->getAggregations()->exists($key)) {
            $aggregation = $entity->getAggregations()->get($key); 
            
            if ($aggregation->isArray()) {
                return $this->createCollection($aggregation->getClass(), $value);
            } else {
                return $this->createEntity($aggregation->getClass(), $value);
            }
        }
        
        return $value; // Retornando el valor del atributo predeterminado
    }
    
    /**
     * 
     * @param string $class
     * @param type $value
     * @return IEntity|null
     */
    private function createEntity(string $class, $value): ?IEntity {
        return $this->ofArray($value, new $class()); // Retornando entidad generada
    }
    
    /**
     * 
     * @param string $class
     * @param mixed $value
     * @return IEntityCollection 
     */
    private function createCollection(string $class, $value): IEntityCollection {
        $array = $this->getCollection(); // Colección 
        
        foreach ($value as $item) {
            $array->add($this->createEntity($class, $item));
        } // Cargando entidades del listado
        
        return $array; // Retornando entidades generadas
    }
    
    /**
     * 
     * @return IEntityCollection|null
     */
    protected function getCollection(): ?IEntityCollection {
        return null;
    }
}