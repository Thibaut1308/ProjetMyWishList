<?php

namespace mywishlist\controleur;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use mywishlist\models\Item;
use mywishlist\vue\VueParticipant;

class ControleurItem
{
    private $c;
    private $htmlvars;

    public function __construct(\Slim\Container $c)
    {
        $this->c = $c;
        $this->htmlvars = [
            'home' => $this->c->router->pathFor('home', []),
            'affichage' => $this->c->router->pathFor('affichage', []),
            'creation' => $this->c->router->pathFor('new', [])
        ];
    }

    function getItem(Request $rq, Response $rs, array $args ):Response {
        $iditem = $args['id'];
        $item = Item::find($iditem);
        $vue = new VueParticipant([$item]);
        $this->htmlvars['basepath'] = $rq->getUri()->getBasePath();

        $rs->getBody()->write($vue->render(3, $this->htmlvars));
        return $rs;
    }

    function getAccueil(Request $rq, Response $rs, array $args): Response {
        $vue = new VueParticipant([]);
        $this->htmlvars['basepath'] = $rq->getUri()->getBasePath();

        $rs->getBody()->write($vue->render(0, $this->htmlvars));
        return $rs;
    }
}