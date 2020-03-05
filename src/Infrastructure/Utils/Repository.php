<?php

namespace Koldown\Hexagonal\Infrastructure\Utils;

use Illuminate\Database\Eloquent\Collection;

use Koldown\Hexagonal\Domain\Contracts\IRepository;
use Koldown\Hexagonal\Domain\Contracts\IEntity;
use Koldown\Hexagonal\Domain\Contracts\IEntityCollection;
use Koldown\Hexagonal\Domain\Utils\EntityMapper;
use Koldown\Hexagonal\Infrastructure\Utils\Model;
use Koldown\Hexagonal\Infrastructure\Utils\Resource;

class Repository implements IRepository {
    
    // Atributos de la clase Repository

    /**
     *
     * @var string 
     */
    protected $entity;
    
    /**
     *
     * @var string 
     */
    private $context;
    
    /**
     *
     * @var EntityMapper 
     */
    private $entityMapper;
    
    // Constructor de la clase Repository
    
    public function __construct(string $classEntity) {
        $this->entity = $classEntity;
    }

    // Métodos sobrescritos de la interfaz IRepository

    public function setContext(string $context): void {
        $this->context = $context;
    }

    public function getContext(): ?string {
        return $this->context;
    }

    public function getEntity(): ?IEntity {
        return new $this->entity();
    }

    public function insert(IEntity $entity): void {
        $hidrations = $entity->getAggregations()->getHidrationKeys();
        
        $model      = $this->getResource()->insert($entity->toArray(), $hidrations);
        
        $this->mapper($model, $entity); // Actualizando entity generada
    }

    public function find($id): ?IEntity {
        return $this->createEntity($this->getResource()->find($id));
    }

    public function findAll(): IEntityCollection {
        return $this->createCollection($this->getResource()->rows());
    }

    public function fetch($id, array $aggregations = null): ?IEntity {
        if (is_null($aggregations)) {
            $aggregations = $this->getEntity()->getAggregations()->getCompositionKeys();
        } // Estableciendo relaciones predeterminadas
        
        return $this->createEntity($this->getResource()->record($id, $aggregations));
    }

    public function fetchAll(array $aggregations = null): IEntityCollection {
        if (is_null($aggregations)) {
            $aggregations = $this->getEntity()->getAggregations()->getCompositionKeys();
        } // Estableciendo relaciones predeterminadas
        
        return $this->createCollection($this->getResource()->catalog($aggregations));
    }
    
    public function resources(): IEntityCollection {
        $entity       = $this->getEntity(); // Entidad que gestiona recurso
        $aggregations = $entity->getAggregations()->getCompositionKeys();
        
        return $this->createCollection($this->getResource($entity)->catalog($aggregations));
    }

    public function update($id, array $data): void {
        $this->getResource()->update($id, $data);
    }

    public function save(IEntity $entity): void {
        $hidrations = $entity->getAggregations()->getHidrationKeys();
        
        $modelo     = $this->getResource()->update($entity->getPrimaryKey(), $entity->toArray(), $hidrations);
        
        $this->mapper($modelo, $entity); // Actualizando entity generada
    }
    
    public function delete(IEntity $entity): void {
        $this->getResource()->delete($entity->getPrimaryKey());
    }
    
    // Métodos de la clase Repository
    
    /**
     * 
     * @param EntityMapper $entityMapper
     * @return void
     */
    public function setMapper(EntityMapper $entityMapper): void {
        $this->entityMapper = $entityMapper;
    }

    /**
     * 
     * @return EntityMapper
     */
    public function getMapper(): EntityMapper {
        if (is_null($this->entityMapper)) {
            $this->entityMapper = new EntityMapper();
        } // Instanciando mapeador del repositorio
        
        return $this->entityMapper; // Retornando mapeador
    }

    /**
     * 
     * @param IEntity|null $entity
     * @return Resource
     */
    protected function getResource(?IEntity $entity = null): Resource {
        if (is_null($entity)) {
            $entity = $this->getEntity();
        } // Definiendo entity
        
        $recurso = (new Resource($entity->getTable()));
        $recurso->setContext($this->getContext());
        
        return $recurso; // Retornando recurso de la entity
    }
    
    /**
     * 
     * @param Model $model
     * @param IEntity $entity
     * @return void
     */
    protected function mapper(Model $model, IEntity $entity): void {
        if (is_null($model)) {
            return;
        } // No se debe realizar mapeo
        
        $this->getMapper()->clean()->ofArray($model->toArray(), $entity);
    }
    
    /**
     * 
     * @param IModelo|null $source
     * @param string|null $class
     * @return IEntity|null
     */
    protected final function createEntity(?IModelo $source, ?string $class = null): ?IEntity {
        if (is_null($source)) { 
            return null; // Modelo se encuentra indefinido
        } 
        
        $entity = (is_null($class)) ? $this->getEntity() : new $class();
        
        $this->mapper($source, $entity);
        
        return $entity; // Retornando la entidad generada
    }
    
    /**
     * 
     * @param Collection $models
     * @param string|null $class
     * @return IEntityCollection
     */
    protected final function createCollection(Collection $models, ?string $class = null): IEntityCollection {
        $entities = $this->getCollection(); // Colección de entidades
        
        foreach ($models as $model) {
            $entities->add($this->createEntity($model, $class));
        } // Mapeando listado de modelos
        
        return $entities; // Retornado listado de entidades generado
    }
    
    /**
     * 
     * @return IEntityCollection|null
     */
    protected function getCollection(): ?IEntityCollection {
        return null;
    }
}