<?php


namespace mywishlist\controleur;


use Slim\Container;
use mywishlist\vue\VueParticipant;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use mywishlist\models\Liste;

/**
 * Pour l'affichage public
 * Class ControleurMain
 * @package mywishlist\controleur
 */
class ControleurMain
{
    private Container $c;
    private $htmlvars;

    public function __construct(Container $c) {
        $this->c = $c;
        $this->htmlvars = [
            'home' => $this->c->router->pathFor('home', []),
            'affichage' => $this->c->router->pathFor('affichage', []),
            'creation' => $this->c->router->pathFor('new', []),
            'compte' => $this->c->router->pathFor('compte', [])
        ];
    }

    /**
     * Méthode gérant l'affichage des listes publiques
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    function getHomeAffichage(Request $rq, Response $rs, array $args): Response {
        $listes = Liste::where('public','=', 1)
            ->where('token', '!=', 'NULL')
            ->where('expiration', '>', date('Y-m-j'))
            ->orderBy('expiration')
            ->get();
        $vue = new VueParticipant([$listes], $this->c);
        $this->htmlvars['basepath'] = $rq->getUri()->getBasePath();
        $rs->getBody()->write($vue->render(1, $this->htmlvars));
        return $rs;
    }

}