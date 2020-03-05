<?php

namespace Koldown\Hexagonal\Infrastructure\Contracts;

use Illuminate\Database\Eloquent\Collection;

use Koldown\Hexagonal\Infrastructure\Utils\Model;

interface IResource {
    
    // Métodos de la interfaz IResource
    
    /**
     * 
     * @param string $context
     * @return void 
     */
    public function setContext(string $context): void;
    
    /**
     * 
     * @return string|null
     */
    public function getContext(): ?string;
    
    /**
     * @return Model|null
     */
    public function getModel(): ?Model;

    /**
     * 
     * @param array $data
     * @param mixed $hidrations
     * @return Model
     */
    public function insert(array $data, $hidrations = null): Model;

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
     * @return Model|null
     */
    public function find($id = null, array $columns = ["*"]): ?Model;
    
    /**
     * 
     * @param mixed $id
     * @param array $aggregations
     * @return Model|null
     */
    public function record($id = null, array $aggregations = []): ?Model;
    
    /**
     * 
     * @param mixed $id
     * @param array $data
     * @param mixed $hidrations
     * @return Model|null
     */
    public function update($id, array $data, $hidrations = null): ?Model;
    
    /**
     * 
     * @param mixed $id
     * @return bool
     */
    public function delete($id = null): bool;
    
    /**
     * 
     * @param array $conditions
     * @return IResource
     */
    public function setConditions(array $conditions): IResource;
    
    /**
     * 
     * @param string $column
     * @param bool $asc
     * @return IResource
     */
    public function orderBy(string $column, bool $asc = true): IResource;
}