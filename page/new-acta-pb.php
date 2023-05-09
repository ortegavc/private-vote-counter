<?php

$username = Utils::getUsername();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (filter_has_var(INPUT_POST, 'numero') and strlen(filter_input(INPUT_POST, 'numero')) > 0) {
        $dao = new ActaDao();
        $record = $dao->findByNumero(strtoupper(trim(filter_input(INPUT_POST, 'numero'))));
        if ($record) {
            $yaexiste = true;
        } else {
            $details = array();
            $total_fh = intval(filter_input(INPUT_POST, 'total')); // Total firmas y huellas
            $t_nomina = 0; // Sumatoria de votos nominales
            $t_blancos = intval(filter_input(INPUT_POST, 'blancos'));
            $t_nulos = intval(filter_input(INPUT_POST, 'nulos'));
            $escorrecta = null;
            $vn = 0;
            $vl = 0;
            // Generar detalle y validar
            foreach ($_POST as $k => $v) {

                /* obtener votos nominales y en lista por candidato */
                if (stristr($k, 'vl') or stristr($k, 'vn')) {

                    $t_nomina += intval($v);
                    $id = explode('_', $k);
                    $fk = array_search($id[1], array_column($details, ':candidatos_idcandidato')); // found key
                    //var_dump($fk);
                    if (is_numeric($fk)) {
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
                            ':actas_idacta' => null,
                            ':candidatos_idcandidato' => $id[1],
                            ':votosnominales' => ($id[0] == 'vn') ? intval($v) : 0,
                            ':votoslista' => ($id[0] == 'vl') ? intval($v) : 0,
                        );
                    }
                }

                ($total_fh == ($t_nomina + $t_nulos + $t_blancos)) ? $escorrecta = 'S' : $escorrecta = 'N';

                $head = array(
                    ':numero' => strtoupper(trim(filter_input(INPUT_POST, 'numero'))),
                    ':tipo' => trim(filter_input(INPUT_POST, 'tipocandidato')),
                    ':tfirmas' => $total_fh,
                    ':votosblancos' => $t_blancos,
                    ':votosnulos' => $t_nulos,
                    ':escorrecta' => $escorrecta,
                    ':username' => $username,
                );
            }

            $last_id = $dao->insert($head, $details);
            Flash::addFlash('Se ha registrado acta con Id No. ' . $head[':numero']);
        }
    } else {
        Flash::addFlash('El número de acta está vacío.');
    }
}

$distrito = Utils::getUrlParam('dis');
$daoListas = new OrgpoliticaDao();
$listas = $daoListas->find();
if (!empty($listas)) {
    $daoCandidatos = new CandidatoDao();
    $search = new CandidatoSearchCriteria();
    $search->setDistrito($distrito);
    $tipo = '';
    if ($distrito == 'R') {
        $search->setTipo('CR');
        $tipo = 'RURALES';
    } else {
        $search->setTipo('CU');
        $tipo = 'URBANOS';
    }

    for ($i = 0; $i < count($listas); $i++) {
        $search->setCodigolista($listas[$i]['codigolista']);
        $listas[$i]['candidatos'] = $daoCandidatos->find($search);
    }
}