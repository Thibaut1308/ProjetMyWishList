<?php


namespace mywishlist\controleur;


use Slim\Container;
use mywishlist\vue\VueParticipant;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ControleurMain
{
    private Container $c;

    public function __construct(Container $c) {
        $this->c = $c;
    }

    function getHomeAffichage(Request $rq, Response $rs, array $args): Response {
        $vue = new VueParticipant([]);
        $htmlvars = [
            'basepath' => $rq->getUri()->getBasePath(),
            'home' => $this->c->router->pathFor('home', []),
            'affichage' => $this->c->router->pathFor('affichage', [])
        ];

        $rs->getBody()->write($vue->render(1, $htmlvars));
        return $rs;
    }

}