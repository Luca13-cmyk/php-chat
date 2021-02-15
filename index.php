<?php 

session_start();

require_once("vendor/autoload.php");

use Slim\Factory\AppFactory;



$app = AppFactory::create();

// Add Routing Middleware
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);



// require_once("site.php");

// ####### ADMIN

require_once("homePage.php");


$app->run();


 ?>

