<?php

/**
 * Description of ActaDao
 *
 * @author ortegavc
 */
final class ActaDao {

    /** @var PDO */
    private $db = null;

    public function __destruct() {
        // close db connection
        $this->db = null;
    }

    public function countPaginado($tipo, $escorrecta, $username) {
        $sql = 'SELECT COUNT(*) as total FROM `actas` WHERE 1';
        if($tipo!= NULL){
            $sql .= ' AND `tipo` = ' . $this->getDb()->quote($tipo);
        }
        if (in_array($escorrecta, ['N', 'S'])) {
            $sql .= ' AND `escorrecta` = ' . $this->getDb()->quote($escorrecta);
        }
        if ($username != 'T') {
            $sql .= ' AND `username` = ' . $this->getDb()->quote($username);
        }
        $row = $this->query($sql)->fetch();
        if (!$row) {
            return null;
        }
        return $row;
    }

    public function findById($id) {
        $row = $this->query('SELECT * FROM `actas` WHERE `idacta` = ' . (int) $id)->fetch();
        if (!$row) {
            return null;
        }
        return $row;
    }

    public function findByNumero($numero) {
        $row = $this->query('SELECT * FROM `actas` WHERE `numero` = ' . $this->getDb()->quote($numero))->fetch();
        if (!$row) {
            return null;
        }
        return $row;
    }

    public function findByTipo($tipo) {
        $result = array();
        $sql = 'SELECT * FROM `actas` WHERE `tipo` = ' . $this->getDb()->quote($tipo) . ' ORDER BY `fecharegistro` DESC';
        foreach ($this->query($sql) as $row) {
            $result[] = $row;
        }
        return $result;
    }

    public function getCountByTipo($tipo) {
        $row = $this->query('SELECT COUNT(*) as total FROM `actas` WHERE `tipo` = ' . $this->getDb()->quote($tipo))->fetch();
        if (!$row) {
            return null;
        }
        return $row;
    }

    public function getCandidadtosByActaLista($idacta, $codigolista) {
        $sql = "SELECT c.ordenpapeleta, c.idcandidato, p.nombre, d.votosnominales, d.votoslista FROM acta_detail d\n"
                . "INNER JOIN candidatos c on d.candidatos_idcandidato = c.idcandidato\n"
                . "INNER JOIN personas p on c.personas_idpersona = p.idpersona\n"
                . "WHERE d.actas_idacta = $idacta\n"
                . "AND c.listas_codigolista =" . $this->getDb()->quote($codigolista) . "\n"
                . "ORDER BY c.ordenpapeleta";
        return $this->query($sql)->fetchAll();
    }

    /*
      SELECT DISTINCT(acta_detail.votoslista), actas_idacta
      FROM `acta_detail`
      INNER JOIN candidatos c ON acta_detail.candidatos_idcandidato = c.idcandidato
      WHERE c.tipocandidatura = 'CU' AND c.distrito = '2'
      HAVING acta_detail.votoslista > 0;
     */

    /**
     * 
     * @param type $distrito
     * @param type $tipocand
     * @return type
     * @deprecated since version 1.0.0
     */
    public function getDisctinctVotosLista($distrito, $tipocand) {
        $sql = "SELECT DISTINCT(acta_detail.votoslista), actas_idacta" . PHP_EOL
                . "FROM `acta_detail`" . PHP_EOL
                . "INNER JOIN candidatos c ON acta_detail.candidatos_idcandidato = c.idcandidato" . PHP_EOL
                . "WHERE c.tipocandidatura = '$tipocand' AND c.distrito = '$distrito'" . PHP_EOL
                . "HAVING acta_detail.votoslista > 0" . PHP_EOL;
        return $this->query($sql)->fetchAll();
    }

    public function getListasById($id) {
        $sql = "SELECT l.codigolista, l.ordenpapeleta FROM actas a\n"
                . "INNER JOIN acta_detail d on a.idacta = d.actas_idacta\n"
                . "INNER JOIN candidatos c on d.candidatos_idcandidato = c.idcandidato\n"
                . "INNER JOIN listas l on c.listas_codigolista = l.codigolista\n"
                . "WHERE a.idacta = $id\n"
                . "GROUP BY l.codigolista, l.ordenpapeleta\n"
                . "ORDER BY l.ordenpapeleta";
        return $this->query($sql)->fetchAll();
    }

    public function getMainTotalsByTipo($tipo) {
        $sql = "SELECT SUM(tfirmas) as tfirmas, SUM(votosblancos) as tvb, SUM(votosnulos) as tvn FROM actas a WHERE tipo = " . $this->getDb()->quote($tipo);
        $row = $this->query($sql)->fetch();
        if (!$row) {
            return null;
        }
        return $row;
    }

    public function getSumVNCandidatoByTipo($tipo) {
        $result = array();
        $sql = "SELECT d.candidatos_idcandidato, p.nombre, SUM(d.votosnominales) FROM actas a INNER JOIN acta_detail d on a.idacta = d.actas_idacta INNER JOIN candidatos c ON d.candidatos_idcandidato = c.idcandidato inner join personas p ON c.personas_idpersona = p.idpersona WHERE a.tipo = '$tipo' GROUP BY d.candidatos_idcandidato, p.nombre";
        foreach ($this->query($sql) as $row) {
            $result[] = $row;
        }
        return $result;
    }

    /**
     * 
     * @param string $distrito
     * @param string $tipocandi
     */
    public function getSumVotosLista($distrito, $tipocandi) {
        $sql = "SELECT SUM(dvl.votoslista) AS sumvl "
                . "FROM (SELECT DISTINCT c.listas_codigolista, actas_idacta, votoslista "
                . "FROM acta_detail d "
                . "INNER JOIN candidatos c ON d.candidatos_idcandidato = c.idcandidato "
                . "WHERE c.distrito = '$distrito' "
                . "AND c.tipocandidatura = '$tipocandi' "
                . "AND d.votoslista > 0) AS dvl";
        $row = $this->query($sql)->fetchColumn();
        if (!$row) {
            return null;
        }
        return $row;
    }

    public function getTotalConcejalesRurales() {
        $sql = "SELECT c.listas_codigolista AS lista, p.nombre, SUM(d.votosnominales + votoslista) AS votos
FROM acta_detail d
INNER JOIN candidatos c ON d.candidatos_idcandidato = c.idcandidato
INNER JOIN personas p ON c.personas_idpersona = p.idpersona
WHERE c.tipocandidatura = 'CR'
GROUP BY lista
HAVING votos > 0
ORDER BY votos DESC;";
        return $this->query($sql)->fetchAll();
    }

    /*
      SELECT c.listas_codigolista AS lista,SUM(d.votosnominales + votoslista) AS votos
      FROM acta_detail d
      INNER JOIN candidatos c ON d.candidatos_idcandidato = c.idcandidato
      WHERE  c.tipocandidatura = 'CU' AND c.distrito = '2'
      GROUP BY lista
      HAVING votos > 0
      ORDER BY votos DESC;
     */

    /**
     * 
     * @param type $distrito
     * @return type
     * @deprecated since version 1.0.0
     */
    public function getTotalXlistaByDistrito($distrito) {
        $sql = "SELECT" . PHP_EOL
                . "c.listas_codigolista AS lista," . PHP_EOL
                . "SUM(d.votosnominales + votoslista) AS votos" . PHP_EOL
                . "FROM acta_detail d" . PHP_EOL
                . "INNER JOIN candidatos c ON d.candidatos_idcandidato = c.idcandidato" . PHP_EOL
                . "WHERE c.tipocandidatura = 'CU' AND c.distrito = '$distrito'" . PHP_EOL
                . "GROUP BY lista" . PHP_EOL
                . "HAVING votos > 0" . PHP_EOL
                . "ORDER BY votos DESC";
        return $this->query($sql)->fetchAll();
    }

    /**
     * 
     * @param string $distrito
     * @param string $tipocandi
     * @param integer $trespr
     */
    public function getTotalXlistaByDistritoTresPr($distrito, $tipocandi, $trespr) {
        $sql = "SELECT 
    c.listas_codigolista AS lista,
    SUM(d.votosnominales + votoslista) AS votos
FROM
    acta_detail d
        INNER JOIN
    candidatos c ON d.candidatos_idcandidato = c.idcandidato
WHERE
    c.tipocandidatura = '$tipocandi'
        AND c.distrito = '$distrito'
        AND c.listas_codigolista IN (SELECT 
            dvl.listas_codigolista
        FROM
            (SELECT DISTINCT
                c.listas_codigolista, actas_idacta, votoslista
            FROM
                acta_detail d
            INNER JOIN candidatos c ON d.candidatos_idcandidato = c.idcandidato
            WHERE
                c.tipocandidatura = '$tipocandi'
                    AND c.distrito = '$distrito'
                    AND d.votoslista > 0) AS dvl
        GROUP BY dvl.listas_codigolista
        HAVING SUM(dvl.votoslista) >= $trespr)
GROUP BY lista
ORDER BY votos DESC";
        return $this->query($sql)->fetchAll();
    }

    /*
      SELECT c.listas_codigolista AS lista, p.nombre, SUM(d.votosnominales + votoslista) AS votos
      FROM acta_detail d
      INNER JOIN candidatos c ON d.candidatos_idcandidato = c.idcandidato
      INNER JOIN personas p ON c.personas_idpersona = p.idpersona
      WHERE  c.tipocandidatura = 'CU' AND c.distrito = '2' and c.listas_codigolista = '127-88-21'
      GROUP BY lista, nombre
      HAVING votos > 0
      ORDER BY votos DESC
      LIMIT 0, 2;
     */

    public function getConcejalesSeleccionados($distrito, $lista, $limit) {
        $sql = "SELECT" . PHP_EOL
                . "c.listas_codigolista AS lista," . PHP_EOL
                . "p.nombre," . PHP_EOL
                . "SUM(d.votosnominales + votoslista) AS votos" . PHP_EOL
                . "FROM acta_detail d" . PHP_EOL
                . "INNER JOIN candidatos c ON d.candidatos_idcandidato = c.idcandidato" . PHP_EOL
                . "INNER JOIN personas p ON c.personas_idpersona = p.idpersona" . PHP_EOL
                . "WHERE c.tipocandidatura = 'CU' AND c.distrito = '$distrito' AND c.listas_codigolista = '$lista'" . PHP_EOL
                . "GROUP BY lista, nombre" . PHP_EOL
                . "HAVING votos > 0" . PHP_EOL
                . "ORDER BY votos DESC" . PHP_EOL
                . "LIMIT 0, $limit";
        return $this->query($sql)->fetchAll();
    }

    public function findDetailById($id) {
        $result = array();
        $sql = "SELECT d.idactadetail, c.idcandidato, c.listas_codigolista, p.nombre, d.votosnominales, d.votoslista FROM `acta_detail` d INNER JOIN `candidatos` c ON d.candidatos_idcandidato = c.idcandidato INNER JOIN `personas` p ON c.personas_idpersona = p.idpersona WHERE d.actas_idacta = $id ORDER BY c.ordenpapeleta";
        foreach ($this->query($sql) as $row) {
            $result[] = $row;
        }
        return $result;
    }

    public function getAllRecords($tipo, $escorrecta, $username, $start, $limit) {
        $sql = 'SELECT * FROM `actas` WHERE 1';
        if($tipo != NULL){
            $sql .= ' AND `tipo` = ' . $this->getDb()->quote($tipo);
        }
        if (in_array($escorrecta, ['N', 'S'])) {
            $sql .= ' AND `escorrecta` = ' . $this->getDb()->quote($escorrecta);
        }
        if ($username != 'T') {
            $sql .= ' AND `username` = ' . $this->getDb()->quote($username);
        }
        $sql .= " ORDER BY idacta ASC LIMIT $start, $limit";
        return $this->query($sql)->fetchAll();
    }

    public function insert($head, $details) {
        $last_id = null;
        $sql = 'INSERT INTO actas(numero, tipo, tfirmas, votosblancos, votosnulos, escorrecta, username) VALUES(:numero, :tipo, :tfirmas, :votosblancos, :votosnulos, :escorrecta, :username)';

        try {
            // set the PDO error mode to exception
            $this->getDb()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // begin the transaction
            $this->getDb()->beginTransaction();
            $statement = $this->getDb()->prepare($sql);
            $statement->execute($head);
            $last_id = $this->getDb()->lastInsertId();
            $sql = 'INSERT INTO acta_detail(actas_idacta, candidatos_idcandidato, votosnominales, votoslista)
                  VALUES(:actas_idacta, :candidatos_idcandidato, :votosnominales, :votoslista);';
            foreach ($details as $det) {
                $det['actas_idacta'] = $last_id;
                $statement = $this->getDb()->prepare($sql);
                $statement->execute($det);
            }
            // commit the transaction
            $this->getDb()->commit();
            return $last_id;
        } catch (PDOException $e) {
            // roll back the transaction if something failed
            $this->getDb()->rollback();
            throw new Exception('DB connection error: ' . $e->getMessage());
        }
    }

    public function actualizar($idacta, $head, $details) {
        $rowCount = null;
        $sql = 'UPDATE actas SET '
                . 'tfirmas = :tfirmas, '
                . 'votosblancos = :votosblancos, '
                . 'votosnulos = :votosnulos, '
                . 'escorrecta = :escorrecta '
                . 'WHERE idacta = :idacta';
        try {
            // set the PDO error mode to exception
            $this->getDb()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // begin the transaction
            $this->getDb()->beginTransaction();
            $statement = $this->getDb()->prepare($sql);
            $statement->execute($head);
            $rowCount = $statement->rowCount();
            $sql = 'UPDATE acta_detail SET votosnominales = :votosnominales, votoslista = :votoslista WHERE actas_idacta = :idacta AND candidatos_idcandidato = :idcandidato';
            foreach ($details as $det) {
                error_log(json_encode($det, true));
                $statement = $this->getDb()->prepare($sql);
                $statement->execute($det);
                $rowCount += $statement->rowCount();
            }
            // commit the transaction
            $this->getDb()->commit();
            return $rowCount;
        } catch (PDOException $e) {
            // roll back the transaction if something failed
            $this->getDb()->rollback();
            throw new Exception('DB connection error: ' . $e->getMessage());
        }
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
