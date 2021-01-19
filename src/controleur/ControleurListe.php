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
            'creation' => $this->c->router->pathFor('new', []),
            'compte' => $this->c->router->pathFor('compte', [])
        ];
    }

    function getListe(Request $rq, Response $rs, array $args ):Response {
        $tokenliste = $args['id'];
        $liste = Liste::where('token','=', $tokenliste)->first();
        if(!is_null($liste)) {
            $vue = new VueParticipant([$liste], $this->c);
            $this->htmlvars['basepath'] = $rq->getUri()->getBasePath();
            $rs->getBody()->write($vue->render(2, $this->htmlvars));
        } else {
            $liste = Liste::where('tokenmodif', '=', $tokenliste)->first();
            $vue = new VueCreateur([$liste], $this->c);
            $this->htmlvars['basepath'] = $rq->getUri()->getBasePath();
            $rs->getBody()->write($vue->render(2, $this->htmlvars));
        }
        return $rs;
    }

    public function formulaireListe(Request $rq, Response $response, $args):Response {
        if(!is_null($args['err'], $this->c)) {
            $vue = new VueCreateur([$args['err']], $this->c);
        }else
        {
            $vue = new VueCreateur([], $this->c);
        }

        $this->htmlvars['basepath'] = $rq->getUri()->getBasePath();
        $response->getBody()->write($vue->render(1, $this->htmlvars));
        return $response;
    }

    public function nouvelleListe(Request $rq, Response $response, $args):Response {
        $post = $rq->getParsedBody();
        $titre = filter_var($post['titre']       , FILTER_SANITIZE_STRING) ;
        $description = filter_var($post['description'] , FILTER_SANITIZE_STRING) ;
        $expiration = $post['expiration'];
        if($titre != "" && $description != "") {
            $tokenmodif = openssl_random_pseudo_bytes(32);
            $tokenmodif = bin2hex($tokenmodif);
            $l = new Liste();
            $l->titre = $titre;
            $l->description = $description;
            $l->tokenmodif = $tokenmodif;
            $l->expiration = $expiration;
            if(isset($_SESSION['profile'])) {
                $l->user_id = $_SESSION['profile']['id'];
            }
            $l->save();
            $urlredirection = $this->c->router->pathFor('affichageliste', ['id'=>$tokenmodif]);
        }else
        {
            $urlredirection = $this->c->router->pathFor('formerr', ['err'=>'erreur']);
        }

        return $response->withRedirect($urlredirection);
    }

    /**
     * Méthode de ControleurListe permettant de modifier une liste et de retourner la page de modification de celle-ci.
     * @param Request $rq
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function modifierListe(Request  $rq, Response $response, $args): Response {
        $post = $rq->getParsedBody();
        $id = filter_var($post['id'], FILTER_SANITIZE_NUMBER_INT);
        $nom = filter_var($post['nom'], FILTER_SANITIZE_STRING);
        $description = filter_var($post['description'], FILTER_SANITIZE_STRING);
        $public = filter_var($post['public'], FILTER_SANITIZE_NUMBER_INT);
        $l = Liste::find($id);
        if($public == 1) {
            if($l->public == 1) {
                $l->public = 0;
            }else {
                $l->public = 1;
            }
        }
        $l->titre = $nom;
        $l->description = $description;
        $l->save();
        $urlredirection = $this->c->router->pathFor('affichageliste', ['id' => $l->tokenmodif]);
        return $response->withRedirect($urlredirection);
    }

    public function validerListe(Request  $rq, Response $response, $args): Response {
        $post = $rq->getParsedBody();
        $idliste = $post['id'];
        $l = Liste::find($idliste);
        $token = openssl_random_pseudo_bytes(32);
        $token = bin2hex($token);
        $l->token = $token;
        $l->save();
        $urlredirection = $this->c->router->pathFor('affichageliste', ['id' => $l->tokenmodif]);
        return $response->withRedirect($urlredirection);
    }


}