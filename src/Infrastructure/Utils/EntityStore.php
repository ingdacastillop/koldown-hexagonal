<?php

namespace Koldown\Hexagonal\Infrastructure\Utils;

use SplObjectStorage;

use Koldown\Hexagonal\Infrastructure\Contracts\IEntityStore;

class EntityStore extends SplObjectStorage implements IEntityStore {
    
    // Métodos sobrescritos de la interfaz EntityStore
    
    public function clear(): void {
        $this->removeAll($this);
    }
}