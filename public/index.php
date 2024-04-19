<?php
    use DI\ContainerBuilder;
    use App\modules\App;


    if( !session_id() ) {
        session_start();
    }

    require_once '../vendor/autoload.php';

    $contBuilder = new ContainerBuilder();
    $app = new App($contBuilder);
    $app->router();

?>