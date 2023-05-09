<?php

if (isset($_SESSION['logged_in'])) {
  // remove all session variables
  session_unset();
  // destroy the session 
  session_destroy();
}
Utils::redirect('home');
