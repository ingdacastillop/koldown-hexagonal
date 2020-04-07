<?php

namespace Koldown\Hexagonal\Infrastructure\Utils;

use DB;
use Illuminate\Database\ConnectionInterface;

use Koldown\Hexagonal\Domain\Contracts\IEntity;
use Koldown\Hexagonal\Domain\Contracts\IEntityCollection;
use Koldown\Hexagonal\Domain\Contracts\IAggregationArray;
use Koldown\Hexagonal\Domain\Contracts\IRepository;
use Koldown\Hexagonal\Domain\Utils\AggregationCascade;

use Koldown\Hexagonal\Infrastructure\Contracts\IWorkUnit;
use Koldown\Hexagonal\Infrastructure\Contracts\IEntityStore;
use Koldown\Hexagonal\Infrastructure\Contracts\IRepositoryStore;
use Koldown\Hexagonal\Infrastructure\Utils\Repository;

class WorkUnit implements IWorkUnit {
    
    // Atributos de la clase WorkUnit
    
    /**
     *
     * @var ConnectionInterface 
     */
    private $connection;

    /**
     *
     * @var string
     */
    private $context;
    
    /**
     *
     * @var int 
     */
    private $time;
    
    /**
     *
     * @var IEntityStore 
     */
    protected $entityStore;
    
    /**
     *
     * @var IRepositoryStore 
     */
    protected $repositoryStore;
    
    /**
     *
     * @var IAggregationArray 
     */
    private $aggregationCascade;
    
    // Constructor de la clase WorkUnit
    
    public function __construct() {
        $this->time = time();
    }

    // Métodos sobrescritos de la interfaz IWorkUnit

    public function setContext(string $context): void {
        $this->context = $context;
    }

    public function getContext(): ?string {
        return $this->context;
    }
    
    public function getNow(): int {
        return $this->time;
    }

    public function getRepository(string $classEntity): ?IRepository {
        if (!$this->repositoryStore->exists($classEntity)) {
            $repositorio = $this->getInstanceRepository($classEntity);
            $repositorio->setContext($this->getContext());

            $this->repositoryStore->set($classEntity, $repositorio);
        } // No existe estancia del repositorio, definiendo repositorio
        
        return $this->repositoryStore->get($classEntity); // Retornando repositorio
    }
    
    public function addEntity(IEntity $entity): void {
        $this->entityStore->attach($entity, new EntityStatus(EntityStatus::STATE_DIRTY, clone $entity));
    }
    
    public function addEntities(array $entities): void {
        foreach ($entities as $entity) { 
            if ($entity instanceof IEntity) {
                $this->addEntity($entity); // Cargando entidad del listado
            }
        }
    }
    
    public function persist(IEntity $entity): void {
        $this->insert($entity); // Registrando entidad principal
        
        $aggregations = $this->getAggregationCascade()->ofEntity($entity);
        
        foreach ($aggregations as $aggregations) {
            $this->insertAggregation($entity, $aggregations);
        } // Registrando listado de agregaciones en cascada
    }
    
    public function persists(IEntityCollection $collection): void {
        foreach ($collection as $entity) {
            $this->persist($entity); // Persistiendo entidad de colección
        }
    }
    
    public function safeguard(IEntity $entity): void {
        $this->modify($entity); // Actualizando entidad principal
        
        $aggregations = $this->getAggregationCascade()->ofEntity($entity);
        
        foreach ($aggregations as $aggregation) {
            $this->modifyAggregation($entity, $aggregation);
        } // Actualizando listado de agregaciones en cascada
    }
    
    public function safeguards(IEntityCollection $collection): void {
        foreach ($collection as $entity) {
            $this->safeguard($entity); // Actualizando entidad de colección
        }
    }
                
    public function destroy(IEntity $entity): void {
        $this->entityStore->attach($entity, new EntityStatus(EntityStatus::STATE_REMOVE));
    }
    
    public function destroys(IEntityCollection $collection): void {
        foreach ($collection as $entity) {
            $this->destroy($entity); // Eliminando entidad de colección
        }
    }

    public function transaction(): void {
        $this->getConnection()->beginTransaction(); // Iniciando transacción
    }

    public function commit(): void {
        foreach ($this->entityStore as $entity) {
            $entityStatus = $this->entityStore[$entity]; // Recuperando datos del proceso
            
            switch ($entityStatus->getStatus()) {
                case (EntityStatus::STATE_DIRTY) :
                    $this->update($entity, $entityStatus); // Actualizando
                break;
            
                case (EntityStatus::STATE_NEW) :
                    // Se debería registrar la entidad
                break;
            
                case (EntityStatus::STATE_REMOVE) :
                    $this->delete($entity);
                break;
            }
        }
        
        $this->entityStore->clear(); $this->getConnection()->commit(); // Confirmando comandos
    }

    public function rollback(): void {
        $this->getConnection()->rollback(); // Revertiendo todos los comandos
    }
    
    // Métodos de la clase WorkUnit

    /**
     * 
     * @param IEntityStore $entityStore
     * @return void
     */
    public function setEntityStore(IEntityStore $entityStore): void {
        $this->entityStore = $entityStore;
    }
    
    /**
     * 
     * @param IRepositoryStore $repositoryStore
     * @return void
     */
    public function setRepositoryStore(IRepositoryStore $repositoryStore): void {
        $this->repositoryStore = $repositoryStore;
    }
    
    /**
     * 
     * @return ConnectionInterface
     */
    protected function getConnection(): ConnectionInterface {
        if (is_null($this->connection)) {
            $this->connection = DB::connection($this->getContext());
        } // Definiendo conexión de la transacción 
        
        return $this->connection; // Conexión con base de datos
    }
    
    /**
     * 
     * @param string $classEntity
     * @return IRepository
     */
    protected function getInstanceRepository(string $classEntity): IRepository {
        return new Repository($classEntity);
    }

    /**
     * 
     * @param IEntity $entity
     * @return void
     */
    protected function insert(IEntity $entity): void {
        $this->getRepository(get_class($entity))->insert($entity); $this->addEntity($entity); 
    }

    /**
     * 
     * @param IEntity $parent
     * @param mixed $aggregation
     * @return void
     */
    protected function insertAggregation(IEntity $parent, $aggregation): void {
        if ($aggregation instanceof IEntity) {
            $aggregation->setParentKey($parent->getPrimaryKey()); $this->persist($aggregation);
        } // Agregación del padre es una entidad simple

        if ($aggregation instanceof IEntityCollection) {
            foreach ($aggregation as $entity) {
                $entity->setParentKey($parent->getPrimaryKey()); $this->persist($entity);
            }
        } // Agregación del padre es un listado de entidades
    }
    
    /**
     * 
     * @param IEntity $entity
     * @return void
     */
    protected function modify(IEntity $entity): void  {
        $this->getRepository(get_class($entity))->save($entity);
    }

    /**
     * 
     * @param IEntity $parent
     * @param mixed $aggregation
     * @return void
     */
    protected function modifyAggregation(IEntity $parent, $aggregation): void {
        if ($aggregation instanceof IEntity) {
            $aggregation->setParentKey($parent->getPrimaryKey()); $this->safeguard($aggregation);
        } // Agregación del padre es una entidad simple

        if ($aggregation instanceof IEntityCollection) {
            foreach ($aggregation as $entity) {
                $entity->setParentKey($parent->getPrimaryKey()); $this->safeguard($entity);
            }
        } // Agregación del padre es un listado de entidades
    }

    /**
     * 
     * @param IEntity $entity
     * @param EntityStatus $item
     * @return void
     */
    protected function update(IEntity $entity, EntityStatus $item): void {
        if ($item->getEntity() != $entity) {
            $data = $this->getArrayUpdate($entity, $item->getEntity());

            $this->getRepository(get_class($entity))->update($entity->getPrimaryKey(), $data);
        } // Entidad modificada, requiere ser actualizada en el Repositorio
    }
    
    /**
     * 
     * @param IEntity $entity
     * @param IEntity $clone
     * @return array
     */
    protected function getArrayUpdate(IEntity $entity, IEntity $clone): array {
        $arrayUpdate = []; // Array para actualizar
        
        $arrayEntidad = $entity->toArray();
        $arrayClone   = $clone->toArray();
        
        foreach ($arrayEntidad as $key => $value) {
            if (!isset($arrayClone[$key])) {
                $arrayUpdate[$key] = $value;
            } else if (($value != $arrayClone[$key])) {
                $arrayUpdate[$key] = $value;
            } // Se detecto valor diferente en la clave
        }
        
        return $arrayUpdate; // Retornando datos de actualización
    }
    
    /**
     * 
     * @param IEntity $entity
     * @return void
     */
    protected function delete(IEntity $entity): void {
        $this->getRepository(get_class($entity))->delete($entity);
    }

    /**
     * 
     * @return IAggregationArray
     */
    protected function getAggregationCascade(): IAggregationArray {
        if (is_null($this->aggregationCascade)) {
            $this->aggregationCascade = new AggregationCascade();
        } // Instanciando listador de agregaciones
        
        return $this->aggregationCascade; // Retornando listador de agregaciones
    }
}