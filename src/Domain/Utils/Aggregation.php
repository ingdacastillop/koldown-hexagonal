<?php

namespace Koldown\Hexagonal\Domain\Utils;

use Koldown\Hexagonal\Domain\Contracts\IAggregation;

class Aggregation implements IAggregation {
    
    // Atributos de la clase Aggregation
    
    /**
     *
     * @var string 
     */
    private $class;
    
    /**
     *
     * @var bool 
     */
    private $array;
    
    /**
     *
     * @var bool 
     */
    private $cascade;
    
    /**
     *
     * @var bool 
     */
    private $hidration;
    
    /**
     *
     * @var bool 
     */
    private $composition;
    
    // Constructor de la clase Aggregation
    
    public function __construct(string $class, bool $array = false, bool $cascade = false, bool $hidration = false, bool $composition = true) {
        $this->class = $class; $this->array = $array; $this->cascade = $cascade; $this->hidration = $hidration; $this->composition = $composition;
    }
    
    // Métodos de la clase Aggregation
    
    public function getClass(): ?string {
        return $this->class;
    }
    
    public function isArray(): ?bool {
        return $this->array;
    }
    
    public function isCascade(): ?bool {
        return $this->cascade;
    }
    
    public function isHidration(): ?bool {
        return $this->hidration;
    }
    
    public function isComposition(): ?bool {
        return $this->composition;
    }
}