<?php

session_start();

if(
    !isset($_SESSION['user'])
    || $_SESSION['user']['role'] != 'visiteur'
){
    header('Location: ../core/login.php');
    exit;
}