<?php

namespace Koldown\Hexagonal\Infrastructure\Traits;

use Koldown\Hexagonal\Infrastructure\Contracts\Sql\IQuery;
use Koldown\Hexagonal\Infrastructure\Contracts\Sql\IWhere;
use Koldown\Hexagonal\Infrastructure\Utils\Sql\Where;
use Koldown\Hexagonal\Infrastructure\Utils\Sql\GroupBy;
use Koldown\Hexagonal\Infrastructure\Utils\Sql\OrderBy;
use Koldown\Hexagonal\Infrastructure\Utils\Sql\Limit;
use Koldown\Hexagonal\Infrastructure\Utils\Sql\Offset;

trait QueryClauseTrait {
    
    // Atributos de la clase QueryClauseTrait
    
    /**
     *
     * @var Where 
     */
    private $where;
    
    /**
     *
     * @var GroupBy 
     */
    private $group;
    
    /**
     *
     * @var array 
     */
    private $orders;
    
    /**
     *
     * @var array 
     */
    private $clauses;

    // Métodos del trait QueryClauseTrait
    
    /**
     * 
     * @return IWhere|null
     */
    protected function getWhere(): ?IWhere {
        return $this->where;
    }
    
    /**
     * 
     * @return IWhere
     */
    private function getInstanceWhere() : IWhere {
        if (is_null($this->where)) {
            $this->where = new Where(); // Inicializando
        }
        
        return $this->where; // Where
    }
    
    /**
     * 
     * @return GroupBy|null
     */
    protected function getGroup(): ?GroupBy {
        return $this->group;
    }
    
    /**
     * 
     * @return GroupBy
     */
    private function getInstanceGroup() : GroupBy {
        if (is_null($this->group)) {
            $this->group = new GroupBy(); // Inicializando
        }
        
        return $this->group; // GroupBy
    }
    
    /**
     * 
     * @return array|null
     */
    protected function getOrders(): ?array {
        return $this->orders;
    }
    
    /**
     * 
     * @return array|null
     */
    protected function getClauses(): ?array {
        return $this->clauses;
    }

    // Métodos sobrescritos de la interfaz IQuery

    public function condition(string $column, string $operator, $value, bool $or = false): IQuery {
        $this->getInstanceWhere()->condition($column, $operator, $value, $or); return $this;
    }

    public function equal(string $column, $value, bool $or = false): IQuery {
        return $this->condition($column, "=", $value, $or);
    }

    public function greater(string $column, $value, bool $or = false): IQuery {
        return $this->condition($column, ">", $value, $or);
    }

    public function smaller(string $column, $value, bool $or = false): IQuery {
        return $this->condition($column, "<", $value, $or);
    }

    public function equalGreater(string $column, $value, bool $or = false): IQuery {
        return $this->condition($column, ">=", $value, $or);
    }

    public function equalSmaller(string $column, $value, bool $or = false): IQuery {
        return $this->condition($column, "<=", $value, $or);
    }
    
    public function different(string $column, $value, bool $or = false): IQuery {
        return $this->condition($column, "<>", $value, $or);
    }

    public function in(string $column, $value, bool $or = false): IQuery {
        $this->getInstanceWhere()->in($column, $value, $or); return $this;
    }
    
    public function between(string $column, $value, bool $or = false): IQuery {
        $this->getInstanceWhere()->between($column, $value, $or); return $this;
    }

    public function like(string $column, $value, bool $or = false): IQuery {
        $this->getInstanceWhere()->like($column, $value, $or); return $this;
    }

    public function isNull(string $column, bool $or = false): IQuery {
        $this->getInstanceWhere()->isNull($column, $or); return $this;
    }

    public function isNotNull(string $column, bool $or = false): IQuery {
        $this->getInstanceWhere()->isNotNull($column, $or); return $this;
    }
    
    public function nested(IWhere $where): IQuery {
        $this->getInstanceWhere()->nested($where); return $this;
    }
    
    public function groupBy(...$columns): IQuery {
        $this->getInstanceGroup()->setColumns($columns); return $this;
    }
    
    public function orderBy(string $column, bool $asc = true): IQuery {
        if (is_null($this->orders)) {
            $this->orders = []; // Inicializando listado
        }
        
        array_push($this->orders, new OrderBy($column, $asc)); return $this;
    }
    
    public function limit(int $count): IQuery {
        if (is_null($this->clauses)) {
            $this->clauses = []; // Inicializando claúsulas
        }
        
        array_push($this->clauses, new Limit($count)); return $this;
    }
    
    public function offset(int $count): IQuery {
        if (is_null($this->clauses)) {
            $this->clauses = []; // Inicializando claúsulas
        }
        
        array_push($this->clauses, new Offset($count)); return $this;
    }
}