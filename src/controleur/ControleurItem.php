<?php

namespace mywishlist\controleur;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use mywishlist\models\Item;
use mywishlist\vue\VueParticipant;

class ControleurItem
{
    private $c;

    public function __construct(\Slim\Container $c)
    {
        $this->c = $c;
    }

    function getItem(Request $rq, Response $rs, array $args ):Response {
        $iditem = $args['id'];
        $item = Item::find($iditem);
        $vue = new VueParticipant([$item]);
        $htmlvars = [
            'basepath' => $rq->getUri()->getBasePath(),
            'home' => $this->c->router->pathFor('home', []),
            'affichage' => $this->c->router->pathFor('affichage', [])
        ];

        $rs->getBody()->write($vue->render(3, $htmlvars));
        return $rs;
    }

    function getAccueil(Request $rq, Response $rs, array $args): Response {
        $vue = new VueParticipant([]);
        $htmlvars = [
            'basepath' => $rq->getUri()->getBasePath(),
            'home' => $this->c->router->pathFor('home', []),
            'affichage' => $this->c->router->pathFor('affichage', [])
        ];

        $rs->getBody()->write($vue->render(0, $htmlvars));
        return $rs;
    }


}