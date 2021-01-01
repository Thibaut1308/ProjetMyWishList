<?php


namespace mywishlist\controleur;


use Slim\Container;
use mywishlist\vue\VueParticipant;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ControleurMain
{
    private Container $c;
    private $htmlvars;

    public function __construct(Container $c) {
        $this->c = $c;
        $this->htmlvars = [
            'home' => $this->c->router->pathFor('home', []),
            'affichage' => $this->c->router->pathFor('affichage', []),
            'creation' => $this->c->router->pathFor('new', [])
        ];
    }

    function getHomeAffichage(Request $rq, Response $rs, array $args): Response {
        $vue = new VueParticipant([], $this->c);

        $this->htmlvars['basepath'] = $rq->getUri()->getBasePath();
        $rs->getBody()->write($vue->render(1, $this->htmlvars));
        return $rs;
    }

}