<?php

namespace Koldown\Hexagonal\Infrastructure\Utils;

use Illuminate\Database\Eloquent\Collection;

use Koldown\Hexagonal\Domain\Contracts\IRepository;
use Koldown\Hexagonal\Domain\Contracts\IEntity;
use Koldown\Hexagonal\Domain\Contracts\IEntityCollection;
use Koldown\Hexagonal\Domain\Contracts\IEntityMapper;
use Koldown\Hexagonal\Domain\Utils\EntityMapper;

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
        $hidrations = $entity->getAggregations()->keys()->hidration();
        
        $model = $this->getQuery()->insert($entity->toArray(), $hidrations);
        
        $this->mapper($model, $entity); // Actualizando entity generada
    }

    public function find($id): ?IEntity {
        return $this->createEntity($this->getQuery()->find($id));
    }

    public function findAll(): IEntityCollection {
        return $this->createCollection($this->getQuery()->rows());
    }

    public function fetch($id, array $aggregations = null): ?IEntity {
        if (is_null($aggregations)) {
            $aggregations = $this->getEntity()->getAggregations()->keys()->mappable();
        } // Estableciendo relaciones predeterminadas
        
        return $this->createEntity($this->getQuery()->record($id, $aggregations));
    }

    public function fetchAll(array $aggregations = null): IEntityCollection {
        if (is_null($aggregations)) {
            $aggregations = $this->getEntity()->getAggregations()->keys()->mappable();
        } // Estableciendo relaciones predeterminadas
        
        return $this->createCollection($this->getQuery()->catalog($aggregations));
    }
    
    public function resources(): IEntityCollection {
        $entity       = $this->getEntity(); // Entidad que gestiona recurso
        $aggregations = $entity->getAggregations()->keys()->mappable();
        
        return $this->createCollection($this->getQuery($entity)->catalog($aggregations));
    }

    public function update($id, array $data): void {
        $this->getQuery()->update($id, $data);
    }

    public function save(IEntity $entity): void {
        $hidrations = $entity->getAggregations()->keys()->hidration();
        
        $modelo     = $this->getQuery()->update($entity->getPrimaryKey(), $entity->toArray(), $hidrations);
        
        $this->mapper($modelo, $entity); // Actualizando entity generada
    }
    
    public function delete(IEntity $entity): void {
        $this->getQuery()->delete($entity->getPrimaryKey());
    }
    
    // Métodos de la clase Repository
    
    /**
     * 
     * @param IEntityMapper $entityMapper
     * @return void
     */
    public function setMapper(IEntityMapper $entityMapper): void {
        $this->entityMapper = $entityMapper;
    }

    /**
     * 
     * @return EntityMapper
     */
    public function getMapper(): IEntityMapper {
        if (is_null($this->entityMapper)) {
            $this->entityMapper = $this->getInstanceMapper();
        } // Instanciando mapeador del repositorio
        
        return $this->entityMapper; // Retornando mapeador
    }
    
    /**
     * 
     * @return IEntityMapper
     */
    protected function getInstanceMapper(): IEntityMapper {
        return new EntityMapper();
    }

    /**
     * 
     * @param IEntity|null $entity
     * @return Query
     */
    protected function getQuery(?IEntity $entity = null): Query {
        if (is_null($entity)) {
            $entity = $this->getEntity();
        } // Definiendo entity
        
        $query = new Query($entity->getTable());
        $query->setContext($this->getContext());
        
        return $query; // Retornando query de la Entity
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
     * @param Model|null $source
     * @param string|null $class
     * @return IEntity|null
     */
    protected function createEntity(?Model $source, ?string $class = null): ?IEntity {
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
    protected function createCollection(Collection $models, ?string $class = null): IEntityCollection {
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