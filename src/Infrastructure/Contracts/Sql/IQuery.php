<?php

namespace Koldown\Hexagonal\Infrastructure\Contracts\Sql;

use Illuminate\Database\Eloquent\Collection;

use Koldown\Hexagonal\Infrastructure\Contracts\Sql\IWhere;
use Koldown\Hexagonal\Infrastructure\Utils\Model;

interface IQuery {
    
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
     * 
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
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @param bool $or
     * @return IQuery
     */
    public function condition(string $column, string $operator, $value, bool $or = false): IQuery;
    
    /**
     * 
     * @param string $column
     * @param mixed $value
     * @param bool $or
     * @return IQuery
     */
    public function equal(string $column, $value, bool $or = false): IQuery;
    
    /**
     * 
     * @param string $column
     * @param mixed $value
     * @param bool $or
     * @return IQuery
     */
    public function greater(string $column, $value, bool $or = false): IQuery;
    
    /**
     * 
     * @param string $column
     * @param mixed $value
     * @param bool $or
     * @return IQuery
     */
    public function smaller(string $column, $value, bool $or = false): IQuery;
    
    /**
     * 
     * @param string $column
     * @param mixed $value
     * @param bool $or
     * @return IQuery
     */
    public function equalGreater(string $column, $value, bool $or = false): IQuery;
    
    /**
     * 
     * @param string $column
     * @param mixed $value
     * @param bool $or
     * @return IQuery
     */
    public function equalSmaller(string $column, $value, bool $or = false): IQuery;
    
    /**
     * 
     * @param string $column
     * @param mixed $value
     * @param bool $or
     * @return IQuery
     */
    public function different(string $column, $value, bool $or = false): IQuery;

    /**
     * 
     * @param string $column
     * @param mixed $value
     * @param bool $or
     * @return IQuery
     */
    public function in(string $column, $value, bool $or = false): IQuery;
    
    /**
     * 
     * @param string $column
     * @param mixed $value
     * @param bool $or
     * @return IQuery
     */
    public function like(string $column, $value, bool $or = false): IQuery;
    
    /**
     * 
     * @param string $column
     * @param mixed $value
     * @param bool $or
     * @return IQuery
     */
    public function between(string $column, $value, bool $or = false): IQuery;
    
    /**
     * 
     * @param string $column
     * @param bool $or
     * @return IQuery
     */
    public function isNull(string $column, bool $or = false): IQuery;
    
    /**
     * 
     * @param string $column
     * @param bool $or
     * @return IQuery
     */
    public function isNotNull(string $column, bool $or = false): IQuery;
    
    /**
     * 
     * @param IWhere $where
     * @return IQuery
     */
    public function nested(IWhere $where): IQuery;
    
    /**
     * 
     * @param array $columns
     * @return IQuery
     */
    public function groupBy(...$columns): IQuery;

    /**
     * 
     * @param string $column
     * @param bool $asc
     * @return IQuery
     */
    public function orderBy(string $column, bool $asc = true): IQuery;
    
    /**
     * 
     * @param int $count
     * @return IQuery
     */
    public function limit(int $count): IQuery;
    
    /**
     * 
     * @param int $count
     * @return IQuery
     */
    public function offset(int $count): IQuery;
}