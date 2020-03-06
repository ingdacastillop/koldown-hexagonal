<?php

namespace Koldown\Hexagonal\Infrastructure\Utils;

use Koldown\Hexagonal\Infrastructure\Contracts\IModel;
use Koldown\Hexagonal\Infrastructure\Contracts\IModelMapper;

class ModelMapper implements IModelMapper {
    
    // Atributos de la clase ModelMapper
    
    /**
     *
     * @var ModelMapper 
     */
    private static $instance = null;
    
    // Constructor de la clase ModelMapper
    
    private function __construct() {
        
    }
    
    // Métodos estáticos de la clase ModelMapper

    /**
     * 
     * @return ModelMapper
     */
    public static function getInstance(): ModelMapper {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        } // Instanciando clase ModelMapper
        
        return self::$instance; // Retornando instancia
    }
    
    // Métodos sobrescritos de la interfaz IModelMapper
    
    public function ofArray(?array $source, IModel $destination): ?IModel {
        if (is_null($source) || is_null($destination)) { 
            return null; // No se puede realizar mapeo del modelo
        } 
        
        $result = $this->getFormatArray($source, $destination->getConversions());
        
        foreach ($result as $key => $value) {
            $destination[$key] = $value;
        } // Cargando los datos del objeto en el modelo
        
        return $destination; // Retornando modelo con sus atributos cargados
    }
    
    public function getFormatArray(array $source, array $conversions): array {
        $formatArray = []; // Datos de array formateado resultante
        
        foreach ($source as $key => $value) {
            $formatArray[$key] = $this->getValueKey($key, $value, $conversions);
        } // Recorriendo origen de datos para formatear
        
        return $formatArray; // Retornando array formateado resultante
    }
    
    // Métodos de la clase ModelMapper

    /**
     * 
     * @param string $key
     * @param mixed $value
     * @param array $conversions
     * @return mixed
     */
    private function getValueKey(string $key, $value, array $conversions) {
        if (isset($conversions[$key])) {
            return $this->getValueConvert($conversions[$key], $value);
        } else {
            return $value; // Retornando valor predeterminado
        }
    }
    
    /**
     * 
     * @param string $type
     * @param mixed $value
     * @return mixed
     */
    protected function getValueConvert(string $type, $value) {
        switch ($type) {
            default : return $value; // Valor predeterminado establecido en array
        }
    }
}