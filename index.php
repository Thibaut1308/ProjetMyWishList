<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$config = require_once 'src/conf/settings.php';

$c = new Slim\Container($config);

session_start();
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

$app->get('/creation/{err}',
    function(Request  $req, Response $response, $args): Response {
        $control = new \mywishlist\controleur\ControleurListe($this);
        $response = $control->formulaireListe($req, $response, $args);
        return $response;
    }
)->setName('formerr');

$app->post('/creation',
    function(Request  $req, Response $response, $args): Response {
        $control = new \mywishlist\controleur\ControleurListe($this);
        $response = $control->nouvelleListe($req, $response, $args);
        return $response;
    }
)->setName('new');



$app->post('/modification',
    function(Request $req, Response $response, $args): Response {
        $control = new \mywishlist\controleur\ControleurListe($this);
        $response = $control->modifierListe($req, $response, $args);
        return $response;
    }
)->setName('modifliste');

$app->post('/modification_item',
    function (Request $req, Response $response, $args): Response {
        $control = new \mywishlist\controleur\ControleurItem($this);
        $response = $control->modifierItem($req, $response, $args);
        return $response;
    }
)->setName('modifitem');

$app->post('/valider',
    function(Request $req, Response $response, $args): Response {
        $control = new \mywishlist\controleur\ControleurListe($this);
        $response = $control->validerListe($req, $response, $args);
        return $response;
    }
)->setName('validerliste');


$app->get('/item/{id}',
    function (Request $req, Response $response, $args): Response {
        $controleuritem = new \mywishlist\controleur\ControleurItem($this);
        $response = $controleuritem->getItem($req, $response, $args);
        return $response;
    }
)->setName('afficheritem');

$app->post('/reserver',
    function(Request $req, Response $response, $args): Response {
        $controleuritem = new \mywishlist\controleur\ControleurItem($this);
        $response = $controleuritem->reserverItem($req, $response, $args);
        return $response;
    }
)->setName('reserver');

$app->post('/modifimage',
    function(Request $req, Response $response, $args): Response {
        $controleuritem = new \mywishlist\controleur\ControleurItem($this);
        $response = $controleuritem->modifierImage($req, $response, $args);
        return $response;
    }
)->setName('modifimage');

$app->get('/authentification',
    function (Request $req, Response $response, $args): Response {
        $controleurcompte = new \mywishlist\controleur\ControleurCompte($this);
        $response = $controleurcompte->formulaireAuthentification($req, $response, $args);
        return $response;
    }
)->setName('compte');

$app->post('/formconnection',
    function(Request $req, Response $response, $args): Response {
        $controleurcompte = new \mywishlist\controleur\ControleurCompte($this);
        $response = $controleurcompte->traiterConnection($req, $response, $args);
        return $response;
    }
)->setName('connection');

$app->post('/forminscription',
    function (Request $req, Response $response, $args): Response {
        $controleurcompte = new \mywishlist\controleur\ControleurCompte($this);
        $response = $controleurcompte->traiterInscription($req, $response, $args);
        return $response;
    }
)->setName('inscription');

$app->post('/ajouterlistecompte',
    function(Request $req, Response $response, $args): Response {
        $controleurcompte = new \mywishlist\controleur\ControleurCompte($this);
        $response = $controleurcompte->ajouterListeCompte($req, $response, $args);
        return $response;
    }

)->setName('ajouterliste');

$app->get('/supprimerItem}',
    function (Request $request, Response $response,$args): Response{
        $control = new \mywishlist\controleur\ControleurItem($this);
        $response = $control->supprimerItem($request,$response,$args);
        return $response;
    }
)->setName('suppItem');



$app->run();
