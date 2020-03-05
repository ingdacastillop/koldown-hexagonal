<?php

namespace Koldown\Hexagonal\Infrastructure\Utils;

use Illuminate\Database\Eloquent\Model as ModelLaravel;
use Illuminate\Database\Eloquent\Builder;

use Koldown\Hexagonal\Infrastructure\Contracts\IModel;

class Model extends ModelLaravel implements IModel {
    
    // Atributos de clase Model
    
    /**
     *
     * @var array 
     */
    protected $conversionsDefault = [];
    
    /**
     *
     * @var array 
     */
    protected $columns = [];
    
    /**
     *
     * @var array 
     */
    protected $aggregations = [];

    /**
     *
     * @var array 
     */
    protected $conversions = [];
    
    /**
     *
     * @var array 
     */
    protected $modifiables = [];
    
    /**
     *
     * @var array 
     */
    protected $nulleables = [];
    
    // Métodos de la clase Model
    
    /**
     * 
     * @return string
     */
    public function getColumnPK(): string {
        return $this->primaryKey;
    }

    /**
     * 
     * @param array $array
     * @return void
     */
    public function setArray(array $array): void {
        ModelMapper::getInstance()->ofArray($array, $this);
    }
    
    /**
     * 
     * @param array $array
     * @return array
     */
    public function getFormatArray(array $array): array {
        return ModelMapper::getInstance()->getFormatArray($array, $this->getConversions());
    }
    
    // Métodos sobrescritos de la interfaz IModel

    public function register(array $data): void {
        $this->setArray($data); $this->save();
    }

    public function rows(array $columns = ["*"]): Collection {
        return $this->select($columns)->get();
    }
    
    public function catalog(array $aggregations = array()): Collection {
        return $this->with($aggregations)->get();
    }

    public function find($id, array $columns = ["*"]): ?IModelo {
        return $this->select($columns)->where($this->primaryKey, $id)->first();
    }

    public function record($id, array $aggregations = array()): ?IModelo {
        return $this->where($this->primaryKey, $id)->with($aggregations)->first();
    }

    public function modify($id, array $data): bool {
        return $this->where($this->primaryKey, $id)->update($this->getFormatArray($data));
    }

    public function remove($id): bool {
        return $this->where($this->primaryKey, $id)->delete();
    }
    
    public function setContext(string $context): void {
        $this->setConnection($context);
    }

    public function getConversions(): array {
        return array_merge($this->conversionsDefault, $this->conversions);
    }
    
    public function getColumns(): array {
        return $this->columns;
    }
    
    public function columns(array $columns = array()): Builder {
        return $this->select(array_merge($this->columns, $columns));
    }
    
    public function getAggregations(): array {
        return $this->aggregations;
    }
    
    public function aggregations(array $aggregations = array()): Builder {
        return $this->with(array_merge($this->aggregations, $aggregations));
    }
    
    public function createBuilder(): Builder {
        return $this->newQuery();
    }
    
    // Métodos sobrescritos de la clase ModelLaravel
    
    public function newEloquentBuilder($query) {
        return new ModelBuilder($query);
    }
}