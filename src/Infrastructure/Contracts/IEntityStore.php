<?php

namespace Koldown\Hexagonal\Infrastructure\Contracts;

interface IEntityStore {
    
    // Métodos de la interfaz IEntityStore
    
    /**
     * 
     * @param mixed $object
     * @param mixed $data
     */
    public function attach($object, $data = null);
    
    /**
     * 
     * @param mixed $object
     */
    public function detach($object);
    
    /**
     * 
     * @return void
     */
    public function clear(): void;
}