<?php

$errormsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $dao = new UsuarioDao();
  $usuario = $dao->findByName(trim(filter_input(INPUT_POST, 'inputUsername')));
  if ($usuario === null) {
    $errormsg = 'Usuario/Contraseña no válidos';
  } elseif($usuario['password'] <> trim(filter_input(INPUT_POST, 'inputPassword'))) {
    $errormsg = 'Usuario/Contraseña no válidos';
  } else {
    $_SESSION['logged_in'] = TRUE;
    $_SESSION['username'] = $usuario['username'];
    $_SESSION['userrole'] = $usuario['role'];
    Utils::redirect('home');
  }
}
