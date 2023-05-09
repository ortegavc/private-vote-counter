<?php

/**
 * Acceso a datos de la tabla usuario
 *
 * @author ortegavc
 */
final class UsuarioDao {
  
  /** @var PDO */
  private $db = null;
   
  
  public function __destruct() {
      // close db connection
      $this->db = null;
  }
  
  public function findAll() {
    $sql = 'SELECT * FROM `usuarios` ORDER BY username';
    return $this->query($sql)->fetchAll();
  }
  
  public function findByName($name) {
    $row = $this->query('SELECT * FROM `usuarios` WHERE username = ' . $this->getDb()->quote($name))->fetch();
    if (!$row) {
        return null;
    }    
    return $row;
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
}