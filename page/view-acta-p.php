<?php

$username = Utils::getUsername();
$dao = new ActaDao();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //print_r($_POST);die;

    $row = $dao->findByNumero(strtoupper(trim(filter_input(INPUT_POST, 'numero'))));
    if ($row) {
        $details = array();
        $total_fh = intval(filter_input(INPUT_POST, 'total')); // Total firmas y huellas
        $t_nomina = 0; // Sumatoria de votos nominales
        $t_blancos = intval(filter_input(INPUT_POST, 'blancos'));
        $t_nulos = intval(filter_input(INPUT_POST, 'nulos'));
        $escorrecta = null;
        $vn = 0;
        $vl = 0;
        $details = array();
        $x = 0;
        // Generar detalle y validar
        foreach ($_POST as $k => $v) {

            /* obtener votos nominales y en lista por candidato */
            if (stristr($k, 'vl') or stristr($k, 'vn')) {

                $t_nomina += intval($v);
                $id = explode('_', $k);
                //var_dump($id);
                $fk = array_search($id[1], array_column($details, ':idcandidato')); // found key
                //var_dump($fk);
                if (is_numeric($fk)) {
                    $x++;
                    //var_dump($fk);
                    //echo "found_key: $fk, candidato:".$id[1].PHP_EOL;
                    switch ($id[0]) {
                        case 'vl': $details[$fk][':votoslista'] = intval($v);
                            break;
                        case 'vn': $details[$fk][':votosnominales'] = intval($v);
                            break;
                    }
                } else {
                    $details[] = array(
                        ':votosnominales' => ($id[0] == 'vn') ? intval($v) : 0,
                        ':votoslista' => ($id[0] == 'vl') ? intval($v) : 0,
                        ':idacta' => $row['idacta'],
                        ':idcandidato' => $id[1],
                    );
                }
                /* if($x > 4) {
                  print_r($details);
                  die;
                  } */
            }

            ($total_fh == ($t_nomina + $t_nulos + $t_blancos)) ? $escorrecta = 'S' : $escorrecta = 'N';

            $head = array(
                ':tfirmas' => $total_fh,
                ':votosblancos' => $t_blancos,
                ':votosnulos' => $t_nulos,
                ':escorrecta' => $escorrecta,
                ':idacta' => $row['idacta'],
            );
        }
        $rowCount = $dao->actualizar($row['idacta'], $head, $details);
        if ($rowCount) {
            Flash::addFlash('Se ha actualizado acta No. ' . $row['numero']);
        }
    } else {
        exit("No se encuentra acta para editar.");
    }
}
$acta = Utils::getActaByGetId();
if (in_array($acta['tipo'], ['CR', 'CU'])) {
    switch ($acta['tipo']) {
        case 'CU':
            $tipo = 'URBANOS';
            break;
        case 'CR':
            $tipo = 'RURALES';
            break;
    }

    $listas = $dao->getListasById(Utils::getUrlParam('id'));
    for ($i = 0; $i < count($listas); $i++) {
        $listas[$i]['candidatos'] = $dao->getCandidadtosByActaLista($acta['idacta'], $listas[$i]['codigolista']);
    }
} else {
    Utils::redirect('view-acta', array('id' => $acta['idacta']));
}
