<?php

namespace Koldown\Hexagonal\Infrastructure\Contracts;

use Koldown\Hexagonal\Domain\Contracts\IRepository;

interface IRepositoryStore {
    
    // Métodos de la interfaz IRepositoryStore
    
    /**
     * 
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool;

    /**
     * 
     * @param string $key
     * @param IRepository $repository
     * @return void
     */
    public function set(string $key, IRepository $repository): void;
    
    /**
     * 
     * @param string $key
     * @return IRepository|null
     */
    public function get(string $key): ?IRepository;
}