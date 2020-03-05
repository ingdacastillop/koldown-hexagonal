<?php

namespace Koldown\Hexagonal\Infrastructure\Utils;

use Koldown\Hexagonal\Domain\Contracts\IRepository;
use Koldown\Hexagonal\Infrastructure\Contracts\IRepositoryStore;

class RepositoryStore implements IRepositoryStore {
    
    // Atributos de la clase RepositoryStore
    
    /**
     *
     * @var array 
     */
    private $repositories = [];
    
    // Métodos sobrescritos de la interfaz IRepositoryStore
    
    public function exists(string $key): bool {
        return isset($this->repositories[$key]);
    }
    
    public function set(string $key, IRepository $value): void {
        $this->repositories[$key] = $value;
    }
    
    public function get(string $key): ?IRepository {
        return !$this->exists($key) ? null : $this->repositories[$key];
    }
}