<?php


namespace mywishlist\controleur;

use mywishlist\vue\VueCreateur;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use mywishlist\vue\VueParticipant;
use Slim\Container;
use mywishlist\models\Liste;

class ControleurListe
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

    function getListe(Request $rq, Response $rs, array $args ):Response {
        $tokenliste = $args['id'];
        $liste = Liste::where('token','=', $tokenliste)->first();
        $vue = new VueParticipant([$liste]);
        $this->htmlvars['basepath'] = $rq->getUri()->getBasePath();

        $rs->getBody()->write($vue->render(2, $this->htmlvars));
        return $rs;
    }

    public function formulaireListe(Request $rq, Response $response, $args):Response {
        $vue = new VueCreateur([], $this->c);
        $this->htmlvars['basepath'] = $rq->getUri()->getBasePath();
        $response->getBody()->write($vue->render(1, $this->htmlvars));
        return $response;
    }

    public function nouvelleListe(Request $rq, Response $response, $args):Response {
        $post = $rq->getParsedBody();
        $titre       = filter_var($post['titre']       , FILTER_SANITIZE_STRING) ;
        $description = filter_var($post['description'] , FILTER_SANITIZE_STRING) ;
        $token = openssl_random_pseudo_bytes(32);
        $token = bin2hex($token);
        $l = new Liste();
        $l->titre = $titre;
        $l->description = $description;
        $l->token = $token;
        $l->save();
        $urlredirection = $this->c->router->pathFor('affichageliste', ['id'=>$token]);
        return $response->withRedirect($urlredirection);
    }
}