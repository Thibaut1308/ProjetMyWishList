<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$config = require_once 'src/conf/settings.php';

$c = new Slim\Container($config);

$db = new DB();
$config= parse_ini_file("src/conf/conf.ini");
$db->addConnection($config);

$db->setAsGlobal();
$db->bootEloquent();

$app = new \Slim\App($c);


$app->get('/',
    function (Request $req, Response $res, $args): Response {
        $controleuritem = new mywishlist\controleur\ControleurItem($this);
        $res = $controleuritem->getAccueil($req, $res, $args);
        return $res;
    }
)->setName('home');


$app->get('/liste/{id}/item',
    function (Request $req, Response $response, $args): Response {
        $nom = $args['id'];
        if(is_numeric($nom))
        {
            $response->getBody()->write("<p>Affichage des items de la liste $nom.</p>");
        }
        return $response;
    }
)->setName('affichagelisteitem');


$app->get('/liste/{action}',
    function (Request $req, Response $response, $args): Response {
        $action = $args['action'];
        if($action == "afficherlistes")
        {
            $response->getBody()->write("<p>Affichage de la liste des listes.</p>");
        }
        return $response;
    }
)->setName('listes');

$app->get('/item/{id}',
    function (Request $req, Response $response, $args): Response {
        $controleuritem = new \mywishlist\controleur\ControleurItem($this);
        $response = $controleuritem->getItem($req, $response, $args);
        return $response;
    }
)->setName('afficheritem');

$app->run();
