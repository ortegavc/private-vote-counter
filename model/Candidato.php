<?php

final class Candidato {
    private $idcandidato;
    private $tipocandidatura;
    private $provincia;
    private $canton;
    private $ordenpapeleta;
    private $idpersona;
    private $codiolista;
    
    
    /**
     * @return int <i>null</i> if not persistent
     */
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        if ($this->id !== null && $this->id != $id) {
            throw new Exception('Cannot change identifier to ' . $id . ', already set to ' . $this->id);
        }
        $this->id = (int) $id;
    }
    
}