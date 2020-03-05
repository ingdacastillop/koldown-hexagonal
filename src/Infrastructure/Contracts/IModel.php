<?php

namespace Koldown\Hexagonal\Infrastructure\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface IModel {
    
    // Métodos de la interfaz IModel

    /**
     *
     * @param array $data
     */
    public function register(array $data);

    /**
     * 
     * @param array $columns 
     * @return Collection
     */
    public function rows(array $columns = ["*"]): Collection;

    /**
     * 
     * @param array $aggregations 
     * @return Collection
     */
    public function catalog(array $aggregations = []): Collection;

    /**
     * 
     * @param mixed $id
     * @param array $columns
     * @return IModel|null
     */
    public function find($id, array $columns = ["*"]): ?IModel;

    /**
     * 
     * @param mixed $id
     * @param array $aggregations
     * @return IModel|null
     */
    public function record($id, array $aggregations = []): ?IModel;
    
    /**
     * 
     * @param mixed $id
     * @param array $data
     * @return bool
     */
    public function modify($id, array $data): bool;
    
    /**
     * 
     * @param mixed $id
     * @return bool
     */
    public function remove($id): bool;
    
    /**
     * 
     * @param string $context
     * @return void
     */
    public function setContext(string $context): void;

    /**
     * 
     * @return array
     */
    public function getConversions(): array;
    
    /**
     * 
     * @return array
     */
    public function getColumns(): array;

    /**
     * 
     * @param array $columns
     * @return Builder
     */
    public function columns(array $columns = []): Builder;
    
    /**
     * 
     * @return array
     */
    public function getAggregations(): array;
    
    /**
     * 
     * @param array $aggregations
     * @return Builder
     */
    public function aggregations(array $aggregations = []): Builder;
    
    /**
     * 
     * @return Builder
     */
    public function createBuilder(): Builder;
}