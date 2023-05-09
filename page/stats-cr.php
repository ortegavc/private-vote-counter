<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$username = Utils::getUsername();
$actadao = new ActaDao();
$votosxlista = $actadao->getTotalConcejalesRurales();
//print_r($votosxlista); die;
