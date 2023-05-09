<?php

$username = Utils::getUsername();
$distrito = Utils::getUrlParam('dist');
$title = 'Elecciones seccionales 2019 - Concejales - Distrito ' . $distrito;

$actadao = new ActaDao();
$sumvl = $actadao->getSumVotosLista($distrito, 'CU');
//print_r($sumvl);die;
$porcentaje = intval($sumvl * 0.03);

$votosxlista = $actadao->getTotalXlistaByDistritoTresPr($distrito, 'CU', $porcentaje);
$cocientesolo = 0;
$cocientesxlista = array();
$cocientetodos = array();
foreach ($votosxlista as $r) {
    for ($i = 0; $i < 5; $i++) {
        $cocientesolo = floor($r['votos'] / ($i + 1));
        $cocientesxlista[$r['lista']][] = $cocientesolo;
        $cocientetodos[] = $cocientesolo;
    }
}
sort($cocientetodos, SORT_NUMERIC);
$cocientesaltos = array_slice(array_reverse($cocientetodos), 0, 5);
$asignaciones = array();
foreach ($cocientesxlista as $l => $a) {
    foreach ($a as $v) {
        if (in_array($v, $cocientesaltos)) {
            if (array_key_exists($l, $asignaciones)) {
                $asignaciones[$l] ++;
            } else {
                $asignaciones[$l] = 1;
            }
        }
    }
}
//print_r($asignaciones); die; 
//Array ( [2-8] => 2 [127-88-21] => 1 [3] => 1 [5] => 1 )
$seleccionados = array();
foreach ($asignaciones as $k => $v) {
    $rows = $actadao->getConcejalesSeleccionados($distrito, $k, $v);
    foreach ($rows as $r) {
        $seleccionados[] = $r;
    }
}
//print_r($seleccionados); die;
