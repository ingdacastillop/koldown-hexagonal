<?php

namespace Koldown\Hexagonal\Infrastructure\Utils;

use Koldown\Hexagonal\Domain\Contracts\IEntity;

class EntityStatus {
    
    // Atributos de la clase EntityStatus
    
    /**
     *
     * @var int 
     */
    private $status;
    
    /**
     *
     * @var IEntity 
     */
    private $entity;
    
    // Constantes de la clase EntityStatus
    
    const STATE_DIRTY  = 101;
    
    const STATE_NEW    = 102;
    
    const STATE_REMOVE = 103;
    
    // Constructor de la clase EntityStatus
    
    /**
     * 
     * @param int $status
     * @param IEntity|null $entity
     */
    public function __construct(int $status, ?IEntity $entity = null) {
        $this->status = $status; $this->entity = $entity;
    }
    
    // Métodos de la clase EntityStatus
    
    /**
     * 
     * @param int $status
     * @return void
     */
    public function setStatus(int $status): void {
        $this->status = $status;
    }
    
    /**
     * 
     * @return int
     */
    public function getStatus() {
        return $this->status;
    }
    
    /**
     * 
     * @param IEntity $entity
     * @return void
     */
    public function setEntity(IEntity $entity): void {
        $this->entity = $entity;
    }
    
    /**
     * 
     * @return IEntity
     */
    public function getEntity(): ?IEntity {
        return $this->entity;
    }
}