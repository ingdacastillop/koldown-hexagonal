<?php

namespace Koldown\Hexagonal\Infrastructure\Utils;

use Illuminate\Database\Eloquent\Collection;

use Koldown\Hexagonal\Infrastructure\Contracts\IModel;
use Koldown\Hexagonal\Infrastructure\Contracts\IResource;
use Koldown\Hexagonal\Infrastructure\Utils\Model;
use Koldown\Hexagonal\Infrastructure\Utils\ModelBuilder;

class Resource implements IResource {
    
    // Atributos de la clase Resource
    
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
    
    /**
     *
     * @var array 
     */
    protected $conditions = [];
    
    /**
     *
     * @var array 
     */
    protected $orders = [];

    // Constructor de la clase Resource
    
    public function __construct(string $model) {
        $this->model = $model;
    }

    // Métodos sobrescritos de la interfaz IResource

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
    
    public function setConditions(array $conditions): IResource {
        $this->conditions = $conditions; return $this;
    }
    
    public function orderBy(string $column, bool $asc = true): IResource {
        array_push($this->orders, ["columna" => $column, "asc" => $asc]); return $this;
    }
    
    public function insert(array $data, $hidrations = null): Model {
        $model = $this->getModel(); $model->register($data); // Modelo
        
        return is_null($hidrations) ? $model : empty($hidrations) ? $model : $model->fresh($hidrations);
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
            $this->conditions[$model->getColumnPK()] = $id;
        } // Se debe agregar filtro de PrimaryKey
        
        return $this->getBuilder($model)->select($columns)->first();
    }

    public function record($id = null, array $aggregations = array()): ?Model {
        $model = $this->getModel(); // Modelo del proceso
        
        if (!is_null($id)) {
            $this->conditions[$model->getColumnPK()] = $id;
        } // Se debe agregar filtro de PrimaryKey
        
        return $this->getBuilder($model)->with($aggregations)->first();
    }
    
    public function update($id, array $data, $hidrations = null): ?Model {
        $model = $this->getModel(); // Modelo del proceso
        
        if (!is_null($id)) {
            $this->conditions[$model->getColumnPK()] = $id;
        } // Se debe agregar filtro de PrimaryKey
        
        $rows = $this->getBuilder($model)->update($model->getFormatArray($data));
        
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
            $this->conditions[$model->getColumnPK()] = $id;
        } // Se debe agregar filtro de PrimaryKey
        
        return ($this->getBuilder($model)->delete() > 0);
    }
    
    // Métodos de la clase Resource
    
    /**
     * 
     * @param Model $model
     * @return ModelBuilder
     */
    protected function getBuilder(Model $model): ModelBuilder {
        $builder = $model->createBuilder(); // Builder del modelo
        
        $this->setQueryConditions($builder, $this->conditions);
        $this->setQueryOrders($builder);
        
        return $builder; // Retornando builder generado para proceso
    }
    
    /**
     * 
     * @param ModelBuilder $builder
     * @param array $conditions
     * @return void
     */
    private function setQueryConditions(ModelBuilder $builder, array $conditions): void {
        foreach ($conditions as $key => $value) {
            $this->aggregateCondition($builder, explode("|", $key), $value);
        }
    }
    
    /**
     * 
     * @param ModelBuilder $builder
     * @param array $condition
     * @param mixed $value
     * @return void
     */
    private function aggregateCondition(ModelBuilder $builder, array $condition, $value): void {
        if (count($condition) == 1) {
            if ($condition[0] === Condition::GROUPING) {
                $builder->where(function ($query) use ($value) {
                    $this->setQueryConditions($query, $value);
                });
            } else {
                $builder->where($condition[0], $value);
            }
        } // No se estableció una consulta con filtros operacionales
        
        else {
            switch ($condition[1]) {
                case (Condition::BETWEEN)        : $builder->whereBetween($condition[0], $value); break;
            
                case (Condition::BETWEEN_OR)     : $builder->orWhereBetween($condition[0], $value); break;
            
                case (Condition::IN)             : $builder->whereIn($condition[0], $value); break;
            
                case (Condition::IN_OR)          : $builder->orWhereIn($condition[0], $value); break;
            
                case (Condition::IS_NULL)        : $builder->whereNull($condition[0]); break;
            
                case (Condition::IS_NULL_OR)     : $builder->orWhereNull($condition[0]); break;
            
                case (Condition::IS_NOT_NULL)    : $builder->whereNotNull($condition[0]); break;
            
                case (Condition::IS_NOT_NULL_OR) : $builder->orWhereNotNull($condition[0]); break;
            
                case (Condition::OR)             : $builder->orWhere($condition[0], $condition[2], $value); break;
            
                default                          : $builder->where($condition[0], $condition[1], $value); break;
            }
        }
    }
    
    /**
     * 
     * @param ModelBuilder $builder
     * @return void
     */
    private function setQueryOrders(ModelBuilder $builder): void {
        foreach ($this->orders as $order) {
            $builder->orderBy($order["columna"], $order["asc"] ? "asc" : "desc");
        }
    }
}