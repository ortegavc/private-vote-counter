<?php

/**
 * Description of CandidatoDao
 *
 * @author ortegavc
 */
final class CandidatoDao {  
    /** @var PDO */
    private $db = null;
    
    public function find(CandidatoSearchCriteria $search = null) {
        return $this->query($this->getFindSql($search))->fetchAll();
    }

    
    /**
   * @return PDO
   */
  private function getDb() {
    if ($this->db !== null) {
        return $this->db;
    }
    $config = Config::getConfig('db');
    try {
        $this->db = new PDO($this->getDsn($config), $config['username'], $config['password']);
    } catch (Exception $ex) {
        throw new Exception('DB connection error: ' . $ex->getMessage());
    }
    return $this->db;
  }
  
  private function getDsn($config) {
    return sprintf('mysql:host=%s;dbname=%s', $config['host'], $config['dbname']);
  }

    public function __destruct() {
        // close db connection
        $this->db = null;
    }
    
    /**
     * @return PDOStatement
     */
    private function query($sql) {
        $statement = $this->getDb()->query($sql, PDO::FETCH_ASSOC);
        if ($statement === false) {
            self::throwDbError($this->getDb()->errorInfo());
        }
        return $statement;
    }

    private static function throwDbError(array $errorInfo) {
        // TODO log error, send email, etc.
        throw new Exception('DB error [' . $errorInfo[0] . ', ' . $errorInfo[1] . ']: ' . $errorInfo[2]);
    }
    
  private function getFindSql(CandidatoSearchCriteria $search = null) {
    $sql = 'SELECT c.idcandidato, c.listas_codigolista, c.ordenpapeleta, p.nombre FROM candidatos c INNER JOIN personas p on c.personas_idpersona = p.idpersona WHERE 0 = 0';
    $orderBy = ' ordenpapeleta';
    if ($search !== null) 
    {
      if ($search->getCodigolista() !== null) 
      {
        $sql .= ' AND c.listas_codigolista = ' . $this->getDb()->quote($search->getCodigolista());
      }
      if ($search->getDistrito() !== null) 
      {
        $sql .= ' AND c.distrito = ' . $this->getDb()->quote($search->getDistrito());
      }
      if ($search->getTipo() !== null) 
      {
        $sql .= ' AND c.tipocandidatura = ' . $this->getDb()->quote($search->getTipo());
      }
    }
    $sql .= ' ORDER BY ' . $orderBy;
    return $sql;
  }
}
