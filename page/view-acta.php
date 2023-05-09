<?php

$username = Utils::getUsername();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $dao = new ActaDao();
  $row = $dao->findByNumero(strtoupper(trim(filter_input(INPUT_POST, 'numero'))));
  if($row) {
    $total_fh = intval(filter_input(INPUT_POST, 'total')); // Total firmas y huellas
    $t_nomina = 0; // Sumatoria de votos nominales
    $t_blancos = intval(filter_input(INPUT_POST, 'blancos'));
    $t_nulos = intval(filter_input(INPUT_POST, 'nulos'));
    $escorrecta = null;
    $details = array();
    // Generar detalle y validar
    foreach($_POST as $k => $v) {
      //obtener votos nominales por candidato
      if(stristr($k, 'vn')) {
        $id = explode('_', $k);
        $details[] = array(          
          ':votosnominales' => intval($v),
          ':votoslista' => null,
          ':idacta' => $row['idacta'],
          ':idcandidato' => $id[1],
        );
        $t_nomina += intval($v);
      }
    }
    ($total_fh == ($t_nomina + $t_nulos + $t_blancos))? $escorrecta = 'S': $escorrecta = 'N';
    $head = array (
      ':tfirmas' => $total_fh,
      ':votosblancos' => $t_blancos,
      ':votosnulos' => $t_nulos,
      ':escorrecta' => $escorrecta,
      ':idacta' => $row['idacta'],
    );    
    $rowCount = $dao->actualizar($row['idacta'], $head, $details);    
    if($rowCount) {
      Flash::addFlash('Se ha actualizado acta No. '.$row['numero']);
    }
  }  
}
$acta = Utils::getActaByGetId();
if(in_array($acta['tipo'], ['AL','PR'])) {
  switch($acta['tipo']) {
    case 'PR':
      $acta['tipo'] = 'PREFECTOS';
      break;
    case 'AL':
      $acta['tipo'] = 'ALCALDES';
      break;
    case 'C':
      $acta['tipo'] = 'CONCEJALES';
      break;
  }
  $dao = new ActaDao();
  $acta['details'] = $dao->findDetailById(Utils::getUrlParam('id'));
} else {
  Utils::redirect('view-acta-p', array('id'=>$acta['idacta']));
}
