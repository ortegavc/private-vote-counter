<?php

/**
 * Description of ActaDao
 *
 * @author ortegavc
 */
final class OrgpoliticaDao {
  /** @var PDO */
  private $db = null;   
  
  public function __destruct() {
      // close db connection
      $this->db = null;
  }
  
  public function find() {
    $search = null;
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
  
  private function getFindSql() {
        $sql = 'select l.* from listas l where l.ordenpapeleta is not null';
        $orderBy = ' l.ordenpapeleta';
        $sql .= ' ORDER BY ' . $orderBy;
        return $sql;
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
        
}
