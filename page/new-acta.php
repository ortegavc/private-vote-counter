<?php

$username = Utils::getUsername();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (filter_has_var(INPUT_POST, 'numero') and strlen(filter_input(INPUT_POST, 'numero')) > 0) {
        $dao = new ActaDao();
        $record = $dao->findByNumero(strtoupper(trim(filter_input(INPUT_POST, 'numero'))));
        if ($record) {
            $yaexiste = true;
        } else {
            $total_fh = intval(filter_input(INPUT_POST, 'total')); // Total firmas y huellas
            $t_nomina = 0; // Sumatoria de votos nominales
            $t_blancos = intval(filter_input(INPUT_POST, 'blancos'));
            $t_nulos = intval(filter_input(INPUT_POST, 'nulos'));
            $escorrecta = null;
            // Generar detalle y validar
            foreach ($_POST as $k => $v) {
                /* obtener votos nominales por candidato */
                if (stristr($k, 'vn')) {
                    $id = explode('_', $k);
                    $details[] = array(
                        ':actas_idacta' => null,
                        ':candidatos_idcandidato' => $id[1],
                        ':votosnominales' => intval($v),
                        ':votoslista' => null,
                    );
                    $t_nomina += intval($v);
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

$tipo = Utils::getUrlParam('tipo');
$dao = new CandidatoDao();
$search = new CandidatoSearchCriteria();
$search->setTipo($tipo);
$candidatos = $dao->find($search);
switch ($tipo) {
    case 'AL': $tipod = 'ALCALDES';
        break;
    case 'PR': $tipod = 'PREFECTOS';
        break;
}
