<?php
    use DI\ContainerBuilder;


    if( !session_id() ) {
        session_start();
    }

    require_once '../vendor/autoload.php';
    $contBuilder = new ContainerBuilder();
    $cont = $contBuilder->build();
    $app = $cont->get("App\modules\App");


?>