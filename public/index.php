<?php

    use \Tamtamchik\SimpleFlash\Flash;
    use function Tamtamchik\SimpleFlash\flash;


    if( !session_id() ) {
        session_start();
    }

    require_once '../vendor/autoload.php';
    include '../router/routes.php';
?>