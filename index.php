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

$app->get( '/affichage',
    function (Request $req, Response $res, $args): Response {
        $controleur = new \mywishlist\controleur\ControleurMain($this);
        $res = $controleur->getHomeAffichage($req, $res, $args);
        return $res;
    }
)->setName('affichage');


$app->get('/liste/{id}',
    function (Request $req, Response $response, $args): Response {
        $control = new mywishlist\controleur\ControleurListe($this);
        $response = $control->getListe($req, $response, $args);
        return $response;
    }
)->setName('affichageliste');

$app->get('/creation',
    function(Request  $req, Response $response, $args): Response {
        $control = new \mywishlist\controleur\ControleurListe($this);
        $response = $control->formulaireListe($req, $response, $args);
        return $response;
    }
)->setName('form');

$app->post('/creation',
    function(Request  $req, Response $response, $args): Response {
        $control = new \mywishlist\controleur\ControleurListe($this);
        $response = $control->nouvelleListe($req, $response, $args);
        return $response;
    }
)->setName('new');



/**$app->get('/liste/{action}',
    function (Request $req, Response $response, $args): Response {
        $action = $args['action'];
        if($action == "afficherlistes")
        {
            $response->getBody()->write("<p>Affichage de la liste des listes.</p>");
        }
        return $response;
    }
)->setName('listes');*/

$app->get('/item/{id}',
    function (Request $req, Response $response, $args): Response {
        $controleuritem = new \mywishlist\controleur\ControleurItem($this);
        $response = $controleuritem->getItem($req, $response, $args);
        return $response;
    }
)->setName('afficheritem');

$app->run();
