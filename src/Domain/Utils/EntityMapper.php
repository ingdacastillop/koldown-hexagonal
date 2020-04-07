<?php

namespace Koldown\Hexagonal\Domain\Utils;

use ReflectionClass;

use Koldown\Hexagonal\Domain\Contracts\IEntity;
use Koldown\Hexagonal\Domain\Contracts\IEntityCollection;
use Koldown\Hexagonal\Domain\Contracts\IEntityMapper;

use Koldown\Utils\Str;

class EntityMapper implements IEntityMapper {
    
    // Atributos de la clase EntityMapper
    
    /**
     *
     * @var array 
     */
    private $entities = [];
    
    // Métodos sobrescritos de la interfaz IEntityMapper
    
    public function clean(): IEntityMapper {
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
     * @param ReflectionClass $reflection
     * @param IEntity $entity
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function setValueKeyEntity(ReflectionClass $reflection, IEntity $entity, string $key, $value): void {
        if ($reflection->hasProperty($key)) {
            $this->setValuePropertyEntity($reflection, $entity, $key, $value);
        } else {
            $this->setValueMethodEntity($reflection, $entity, $key, $value);
        }
    }
    
    /**
     * 
     * @param ReflectionClass $reflection
     * @param IEntity $entity
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function setValuePropertyEntity(ReflectionClass $reflection, IEntity $entity, string $key, $value): void {
        $accessor = $reflection->getProperty($key); // Accesor del valor

        if ($accessor->isPublic()) {
            $accessor->setValue($entity, $this->getValue($entity, $key, $value));
        } else {
            $this->setValueMethodEntity($reflection, $entity, $key, $value);
        }
    }
    
    /**
     * 
     * @param ReflectionClass $reflection
     * @param IEntity $entity
     * @param string $key
     * @param mixed $value
     */
    protected function setValueMethodEntity(ReflectionClass $reflection, IEntity $entity, string $key, $value) {
        $setter = Str::getInstance()->getCamelCase()->setter($key); // Método
            
        if ($reflection->hasMethod($setter)) {
            $accessor = $reflection->getMethod($setter);
            
            if ($accessor->isPublic()) {
                $accessor->invoke($entity, $this->getValue($entity, $key, $value));
            } // Asignando valor de la propiedad por método
        }
    }

    /**
     * 
     * @param IEntity $entity
     * @param string $key
     * @param mixed $value
     * @return mixed|null
     */
    protected function getValue(IEntity $entity, string $key, $value) {
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
     * @param string $classEntity
     * @param mixed $value
     * @return IEntity|null
     */
    protected function createEntity(string $classEntity, $value): ?IEntity {
        return $this->ofArray($value, new $classEntity()); // Retornando entidad generada
    }
    
    /**
     * 
     * @param string $classEntity
     * @param mixed $collection
     * @return IEntityCollection 
     */
    protected function createCollection(string $classEntity, $collection): IEntityCollection {
        $array = $this->getCollection(); // Colección 
        
        foreach ($collection as $value) {
            $array->add($this->createEntity($classEntity, $value));
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