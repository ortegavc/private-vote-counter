<?php

$username = Utils::getUsername();
$title = '';
if ($_SESSION['userrole'] <> 'boss') {
  $invalidrole = 'Su cuenta de usuario no tiene privilegios para acceder al dashboard.';
} else {
  try {
    $tipo = Utils::getUrlParam('tipo');
    switch(strtolower($tipo)){
      case 'al':
        $title = 'Elecciones seccionales 2019 - Alcaldes';
        break;
      case 'pr':
        $title = 'Elecciones seccionales 2019 - Prefectos';
        break;
      default:
        $title = 'Tipo no definido';
    }
    $dao = new ActaDao();
    $records = $dao->findByTipo(strtoupper($tipo));
  } catch (Exception $ex) {
      throw new NotFoundException('No ACTA identifier provided.');
  }
}