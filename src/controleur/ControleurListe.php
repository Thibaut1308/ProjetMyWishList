<?php


namespace mywishlist\controleur;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use mywishlist\vue\VueParticipant;
use Slim\Container;
use mywishlist\models\Liste;

class ControleurListe
{
    private Container $c;

    public function __construct(Container $c) {
        $this->c = $c;
    }

    function getListe(Request $rq, Response $rs, array $args ):Response {
        $idliste = $args['id'];
        $liste = Liste::find($idliste);
        $vue = new VueParticipant([$liste]);
        $htmlvars = [
            'basepath' => $rq->getUri()->getBasePath(),
            'home' => $this->c->router->pathFor('home', []),
            'affichage' => $this->c->router->pathFor('afficheritem', ['id'=>$args['id']])
        ];

        $rs->getBody()->write($vue->render(2, $htmlvars));
        return $rs;
    }
}