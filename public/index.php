<?php

if( !session_id() ) {
    session_start();
}

require_once '../vendor/autoload.php';
include '../router/routes.php';
?>