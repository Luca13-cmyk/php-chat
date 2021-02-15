<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use \Hcode\Page;
use \Hcode\Model\User;

$app->get('/', function (Request $request, Response $response, $args) {

    header("Location: /chat");
    exit;
});

$app->get('/chat', function (Request $request, Response $response, $args) {

    $page = new Page([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl("chat");

    return $response;
});
$app->get('/register', function (Request $request, Response $response, $args) {

    $page = new Page([
        "header"=>false,
        "footer"=>false
    ]);
    
    $page->setTpl("wizard-build-profile");

    return $response;
});
$app->get('/login', function (Request $request, Response $response, $args) {

    $page = new Page([
        "header"=>false,
        "footer"=>false
    ]);
    
    $page->setTpl("login");

    return $response;
});

?>