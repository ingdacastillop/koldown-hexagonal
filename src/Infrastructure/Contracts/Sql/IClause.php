<?php

namespace Koldown\Hexagonal\Infrastructure\Contracts\Sql;

use Illuminate\Database\Eloquent\Builder;

interface IClause {
    
    /**
     * 
     * @param Builder $builder
     * @return void
     */
    public function flush(Builder $builder): void;
}