<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$dao = new ActaDao();
$chart = filter_input(INPUT_GET, 'chart');
$tipo = strtoupper(filter_input(INPUT_GET, 'tipo'));
$respuesta = array();
if($chart == 'col') {
  $resultados = $dao->getSumVNCandidatoByTipo($tipo);
  foreach($resultados as $r) {
    $respuesta[] = array('y'=>intval($r['SUM(d.votosnominales)']), 'label'=>utf8_encode($r['nombre']), 'indexLabel'=>'{y}');
  }
} elseif($chart == 'pie') {
  $resultados = $dao->getCountByTipo($tipo);
  if(isset($resultados['total'])){
    $existentes = round((intval($resultados['total']) * 100) / 646, 2);
    $pendientes = 100 - $existentes;
    $respuesta = array(
      array('y' =>$existentes, "legendText" => "Registradas", "label" => "Registradas"),
      array('y' =>$pendientes, "legendText" => "Pendientes", "label" => "Pendientes"),
    );
  }
} elseif($chart == 'pie2') {
  $totalesprincip = $dao->getMainTotalsByTipo($tipo);
  $totalnominales = $dao->getSumVNCandidatoByTipo($tipo);
  foreach($totalnominales as $tvn) {
    $y = round((intval($tvn['SUM(d.votosnominales)']) * 100) / intval($totalesprincip['tfirmas']), 2);
    $respuesta[] = array('y'=>$y/*, 'legendText' => $tvn['nombre']*/, 'label' => $tvn['nombre']);
  }
  $y = round((intval($totalesprincip['tvb']) * 100) / intval($totalesprincip['tfirmas']), 2);
  $respuesta[] = array('y'=>$y/*, 'legendText' => 'VOTOS BLANCOS'*/, 'label' => 'VOTOS BLANCOS');
  $y = round((intval($totalesprincip['tvn']) * 100) / intval($totalesprincip['tfirmas']), 2);
  $respuesta[] = array('y'=>$y/*, 'legendText' => 'VOTOS NULOS'*/, 'label' => 'VOTOS NULOS');
}

echo json_encode($respuesta, JSON_NUMERIC_CHECK);