<?php


namespace mywishlist\controleur;



use mywishlist\vue\VueCompte;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Container;
use mywishlist\models\Liste;

class ControleurCompte
{
    /**
     * ControleurCompte constructor.
     * @param $param
     */
    private Container $c;
    private $htmlvars;

    public function __construct(Container $c)
    {
        $this->c = $c;
        $this->htmlvars = [
            'home' => $this->c->router->pathFor('home', []),
            'affichage' => $this->c->router->pathFor('affichage', []),
            'creation' => $this->c->router->pathFor('new', []),
            'compte' => $this->c->router->pathFor('compte', [])
        ];
    }


    /**
     * Controleur gérant l'espace personnel
     * @param Request $rq
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function formulaireAuthentification(Request $rq, Response $response, $args): Response
    {

        $this->htmlvars['basepath'] = $rq->getUri()->getBasePath();
        if(isset($_SESSION['profile']))
        {
            $userid = $_SESSION['profile']['id'];
            $listes = Liste::where('user_id', '=', $userid )->get();
            $vue = new VueCompte([$listes], $this->c);
            $response->getBody()->write($vue->render(2, $this->htmlvars));
        }else
        {
            $vue = new VueCompte([], $this->c);
            $response->getBody()->write($vue->render(1, $this->htmlvars));
        }
        return $response;
    }

    /**
     * Méthode de traitement de la demande de connexion après envoie du formulaire correspondant
     * @param Request $req
     * @param Response $response
     * @param $args
     * @return mixed
     */
    public function traiterConnection(Request $req, Response $response, $args)
    {
        $post = $req->getParsedBody();
        $motdepasse = $post['motdepasse'];
        $nomutilisateur = $post['utilisateur'];
        Authentification::authenticate($nomutilisateur, $motdepasse);
        $urlredirection = $this->c->router->pathFor('compte');
        return $response->withRedirect($urlredirection);

    }

    /**
     * Méthode de traitement de la demande de l'inscription après envoie du formulaire correspondant
     * @param Request $req
     * @param Response $response
     * @param $args
     * @return mixed
     */
    public function traiterInscription(Request $req, Response $response, $args)
    {
        $post = $req->getParsedBody();
        $motdepasse = $post['motdepasse'];
        $nomutilisateur = $post['utilisateur'];
        Authentification::createUser($nomutilisateur, $motdepasse);
        Authentification::authenticate($nomutilisateur, $motdepasse);
        $urlredirection = $this->c->router->pathFor('compte');
        return $response->withRedirect($urlredirection);

    }

    /**
     * Méthode d'ajout d'une liste à l'espace personnel après envoie du token de modification
     * @param Request $req
     * @param Response $response
     * @param $args
     * @return mixed
     */
    public function ajouterListeCompte(Request $req, Response $response, $args)
    {
        $post = $req->getParsedBody();
        $tokenmodif = $post['tokenmodif'];
        $liste = Liste::where('tokenmodif', '=', $tokenmodif)->first();
        $liste->user_id = $_SESSION['profile']['id'];
        $liste->save();
        $urlredirection = $this->c->router->pathFor('compte');
        return $response->withRedirect($urlredirection);
    }
}