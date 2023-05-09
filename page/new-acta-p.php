<?php

$username = Utils::getUsername();
$dao = new RecintoDao();
$recintos = $dao->find();
