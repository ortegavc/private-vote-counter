<?php

final class SessionValidator {
  private function __construct() {
  }
  
  public static function isLoguedIn() {
    $response = false;
    if (isset($_SESSION['logged_in'])) {
      $response = true;
    }
    return $response;
  }
  
}

/*
private static function isValidIPCanacons($canacons, $remote_addr){
    $ipcanacons = Config::getConfig("ipcanacons");
    if(in_array($remote_addr, explode(",", $ipcanacons[$canacons]))){
        return true;
    }
    return false;
}
*/