<?php

namespace Koldown\Hexagonal\Infrastructure\Utils;

use Illuminate\Database\Eloquent\Collection;

use Koldown\Hexagonal\Infrastructure\Contracts\IModel;
use Koldown\Hexagonal\Infrastructure\Contracts\Sql\IQuery;
use Koldown\Hexagonal\Infrastructure\Contracts\Sql\IWhere;

class Query implements IQuery {
    use \Koldown\Hexagonal\Infrastructure\Traits\QueryClauseTrait;
    
    // Atributos de la clase Query
    
    /**
     *
     * @var string 
     */
    private $context;

    /**
     *
     * @var string 
     */
    protected $model;

    // Constructor de la clase Query
    
    public function __construct(string $model) {
        $this->model = $model; 
    }

    // Métodos sobrescritos de la interfaz IQuery

    public function setContext(string $context): void {
        $this->context = $context;
    }

    public function getContext(): ?string {
        return $this->context;
    }

    public function getModel(): ?Model {
        $model = (new $this->model()); // Instanciando
        
        if ($model instanceof IModel) {
            $model->setContext($this->getContext()); 
        } // Definiendo conexión del contexto
        
        return $model; // Retornando modelo generado
    }
    
    public function insert(array $data, $hidrations = null): Model {
        $model = $this->getModel(); $model->register($data); 
        
        return is_null($hidrations) ? $model : empty($hidrations) ? 
            $model : $model->fresh($hidrations);
    }

    public function rows(array $columns = ["*"]): Collection {
        return $this->getBuilder($this->getModel())->select($columns)->get();
    }

    public function catalog(array $aggregations = array()): Collection {
        return $this->getBuilder($this->getModel())->with($aggregations)->get();
    }

    public function find($id = null, array $columns = ["*"]): ?Model {
        $model = $this->getModel(); // Modelo del proceso
        
        if (!is_null($id)) {
            $this->equal($model->getColumnPK(), $id);
        } // Se debe agregar filtro de PrimaryKey
        
        return $this->getBuilder($model)->select($columns)->first();
    }

    public function record($id = null, array $aggregations = array()): ?Model {
        $model = $this->getModel(); // Modelo del proceso
        
        if (!is_null($id)) {
            $this->equal($model->getColumnPK(), $id);
        } // Se debe agregar filtro de PrimaryKey
        
        return $this->getBuilder($model)->with($aggregations)->first();
    }
    
    public function update($id, array $data, $hidrations = null): ?Model {
        $model = $this->getModel(); // Modelo del proceso
        
        if (!is_null($id)) {
            $this->equal($model->getColumnPK(), $id);
        } // Se debe agregar filtro de PrimaryKey
        
        $rows = $this->getBuilder($model, false)->update($model->getFormatArray($data));
        
        if ($rows > 0) {
            return is_null($hidrations) ? $this->find() : // Sin hidrataciones
                empty($hidrations) ? 
                    $this->find() : $this->record(null, $hidrations);
        } else {
            return null; // No actualizó ningún registro en persistencia
        }
    }
    
    public function delete($id = null): bool {
        $model = $this->getModel(); // Modelo del proceso
        
        if (!is_null($id)) {
            $this->equal($model->getColumnPK(), $id);
        } // Se debe agregar filtro de PrimaryKey
        
        return ($this->getBuilder($model, false)->delete() > 0);
    }
    
    // Métodos de la clase Query
    
    /**
     * 
     * @param Model $model
     * @param bool $isSelect
     * @return ModelBuilder
     */
    protected function getBuilder(Model $model, bool $isSelect = true): ModelBuilder {
        $builder = $model->createBuilder(); // Builder del modelo
        
        $this->flushQueryWhere($builder, $this->getWhere());
        
        if ($isSelect) {
            $this->flushQueryGroups($builder);  // Agrupadores
            $this->flushQueryOrders($builder);  // Ordenadores
            $this->flushQueryClauses($builder); // Claúsulas
        }
        
        return $builder; // Retornando builder generado para proceso
    }
    
    /**
     * 
     * @param ModelBuilder $builder
     * @param IWhere|null $where
     * @return void
     */
    private function flushQueryWhere(ModelBuilder $builder, ?IWhere $where): void {
        if (!is_null($where)) {
            foreach ($where->getPredicates() as $predicate) {
                if ($predicate instanceof IWhere) {
                    $builder->where(function ($query) use ($predicate) {
                        $this->flushQueryWhere($query, $predicate);
                    });
                } else {
                    $predicate->flush($builder); // Cargando condición Where
                }
            }
        }
    }
    
    /**
     * 
     * @param ModelBuilder $builder
     * @return void
     */
    private function flushQueryGroups(ModelBuilder $builder): void {
        if (!is_null($this->getGroup())) {
            $this->getGroup()->flush($builder); // Cargando clausula 'GROUP BY'
        }
    }

    /**
     * 
     * @param ModelBuilder $builder
     * @return void
     */
    private function flushQueryOrders(ModelBuilder $builder): void {
        if (is_array($this->getOrders())) {
            foreach ($this->getOrders() as $orderBy) {
                $orderBy->flush($builder); // Cargando clausula 'ORDER BY'
            }
        }
    }

    /**
     * 
     * @param ModelBuilder $builder
     * @return void
     */
    private function flushQueryClauses(ModelBuilder $builder): void {
        if (is_array($this->getClauses())) {
            foreach ($this->getClauses() as $clause) {
                $clause->flush($builder); // Cargando claúsula
            }
        }
    }
}