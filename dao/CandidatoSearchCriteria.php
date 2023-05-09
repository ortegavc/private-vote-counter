<?php

//namespace Elecciones\Dao;


final class CandidatoSearchCriteria {
  
  private $codigolista = null;
  private $distrito = null;
  private $tipo = null;
    
  /**
   * @return string
   */
  public function getCodigolista() {
    return $this->codigolista;
  }

  /**
   * @return this
   */
  public function setCodigolista($codigolista) {
    $this->codigolista = $codigolista;
    return $this;
  }
    
  
  
    /**
     * @return string
     */
    public function getDistrito() {
        return $this->distrito;
    }

    /**
     * @return this
     */
    public function setDistrito($distrito) {
        $this->distrito = $distrito;
        return $this;
    }
  
  /**
     * @return string
     */
    public function getTipo() {
        return $this->tipo;
    }

    /**
     * @return this
     */
    public function setTipo($tipo) {
        $this->tipo = $tipo;
        return $this;
    }


}

