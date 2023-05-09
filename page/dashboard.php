<?php

$username = Utils::getUsername();
if ($_SESSION['userrole'] <> 'boss') {
  $invalidrole = 'Su cuenta de usuario no tiene privilegios para acceder al dashboard.';
} else {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = filter_input(INPUT_POST, 'tipo');    
    $escorrecta = filter_input(INPUT_POST, 'escorrecta');
    $username = filter_input(INPUT_POST, 'username');
  } else {
    $tipo = NULL;
    $escorrecta = 'T';
    $username = 'T';
  }
  $records = array();
  // Get the requested page. By default grid sets this to 1. 
  $pagn = $_GET['pagn'];
  // get how many rows we want to have into the grid - rowNum parameter in the grid 
  $limit = 20; //$_GET['rows'];
  
  $dao = new ActaDao();
  $usuarioDao = new UsuarioDao();
  $row = $dao->countPaginado($tipo, $escorrecta, $username);
  $count = intval($row['total']);
  // calculate the total pages for the query 
  if( $count > 0 && $limit > 0) { 
    $total_pages = ceil($count/$limit); 
  } else { 
    $total_pages = 0;
  } 

  // if for some reasons the requested page is greater than the total 
  // set the requested page to total page 
  if ($pagn > $total_pages) {
    $pagn = $total_pages;
  }
  
  //print_r($total_pages);die;

  // calculate the starting position of the rows 
  $start = $limit * $pagn - $limit;

  // if for some reasons start position is negative set it to 0 
  // typical case is that the user type 0 for the requested page 
  if($start <0) {
    $start = 0; 
  }  
  $usuarios = $usuarioDao->findAll();
  $records = $dao->getAllRecords($tipo, $escorrecta, $username, $start, $limit);
  //print_r($records);die;
}
