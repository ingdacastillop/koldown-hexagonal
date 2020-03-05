<?php

namespace Koldown\Hexagonal\Domain\Contracts;

interface IAggregation {
    
    // Métodos de la interfaz IAggregation
    
    /**
     * 
     * @return string|null
     */
    public function getClass(): ?string;
    
    /**
     * 
     * @return bool
     */
    public function isArray(): ?bool;
    
    /**
     * 
     * @return bool
     */
    public function isCascade(): ?bool;
    
    /**
     * 
     * @return bool
     */
    public function isHidration(): ?bool;
    
    /**
     * 
     * @return bool
     */
    public function isComposition(): ?bool;
}