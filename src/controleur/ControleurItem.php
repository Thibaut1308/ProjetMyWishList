<?php

namespace mywishlist\controleur;
use mywishlist\models\Liste;
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
            'creation' => $this->c->router->pathFor('new', []),
            'compte' => $this->c->router->pathFor('compte', [])
        ];
    }

    function getItem(Request $rq, Response $rs, array $args ):Response {
        $iditem = $args['id'];
        $item = Item::find($iditem);
        $liste  = Liste::find($item->liste_id);
        $vue = new VueParticipant([$item, $liste], $this->c);
        $this->htmlvars['basepath'] = $rq->getUri()->getBasePath();

        $rs->getBody()->write($vue->render(3, $this->htmlvars));
        return $rs;
    }

    function getAccueil(Request $rq, Response $rs, array $args): Response {
        $vue = new VueParticipant([], $this->c);
        $this->htmlvars['basepath'] = $rq->getUri()->getBasePath();

        $rs->getBody()->write($vue->render(0, $this->htmlvars));
        return $rs;
    }

    public function modifierItem(Request  $rq, Response  $response, $args): Response {
        $post = $rq->getParsedBody();
        $idliste = filter_var($post['id'], FILTER_SANITIZE_NUMBER_INT);
        $iditem = filter_var($post['iditem'], FILTER_SANITIZE_NUMBER_INT);
        $nomitem = filter_var($post['nomitem'], FILTER_SANITIZE_STRING);
        $description = filter_var($post['description'], FILTER_SANITIZE_STRING);
        $urlsite = filter_var($post['urlitem'], FILTER_SANITIZE_URL);
        $tarif = filter_var($post['tarifitem'], FILTER_SANITIZE_NUMBER_FLOAT);
        if (filter_var($post['urlimgitem'], FILTER_VALIDATE_URL)) {
            $urlimg=filter_var($post['urlimgitem'], FILTER_SANITIZE_URL);
        }else {
            $str=filter_var($post['urlimgitem'], FILTER_SANITIZE_STRING);
            $urlimg=$this->htmlvars['basepath']."/ProjetMyWishList/web/img/$str";
        }
        $item = Item::find($iditem);
        if(is_null($item)) { //Nouvel Item
            $item = new Item();
            $item->nom = $nomitem;
            $item->liste_id = $idliste;
            $item->descr = $description;
            $item->url = $urlsite;
            $item->tarif = $tarif;
            $item->img = $urlimg;
        }else
        {
            if($item->liste_id == $idliste)
            {
                $item->nom = $nomitem;
                $item->descr = $description;
                $item->url = $urlsite;
                $item->tarif = $tarif;
            }
        }
        $item->save();
        $liste = Liste::find($idliste);
        $urlredirection = $this->c->router->pathFor('affichageliste', ['id'=> $liste->tokenmodif]);
        return $response->withRedirect($urlredirection);
    }

    public function reserverItem(Request  $rq, Response  $response, $args): Response{
        $post = $rq->getParsedBody();
        $iditem = filter_var($post['id'], FILTER_SANITIZE_NUMBER_INT);
        $nom = filter_var($post['nom'], FILTER_SANITIZE_STRING);
        $message = filter_var($post['message'], FILTER_SANITIZE_STRING);
        setcookie('nom', $nom, time()+3600);
        $item = Item::find($iditem);
        $item->reservation = $nom;
        $item->message = $message;
        $item->save();
        $urlredirection = $this->c->router->pathFor('afficheritem', ['id'=> $iditem]);
        return $response->withRedirect($urlredirection);
    }

    public function modifierImage(Request $req, Response $response, $args)
    {
        filter_input(INPUT_POST, 'iditem', FILTER_SANITIZE_NUMBER_INT);
        $id = $_POST["iditem"];
        filter_input(INPUT_POST, 'imgitem', FILTER_SANITIZE_STRING);
        $imgitem = $_POST["imgitem"];
        filter_input(INPUT_POST, 'urlitem', FILTER_SANITIZE_STRING);
        $urlitem = $_POST["urlitem"];

        $item = Item::find($id);
        if (is_null($item))
        {
            $item = new Item();
            $item->id = $id;
            $item->img = $imgitem;
            $item->url = $urlitem;
        }else
        {
            if ($item->id = $id)
            {
                $item->img = $imgitem;
                $item->url = $urlitem;
            }
        }
        $item->save();
        $urlredirection = $this->c->router->pathFor('afficheritem', ['id'=> $id]);
        return $response->withRedirect($urlredirection);
    }

    public function supprimerItem(Request  $rq, Response $response,array $args): Response {

        $post = $rq->getParsedBody();
        $iditem = filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT);
        $item = Item::find($iditem);
        $l = Liste::find($item->liste_id);
        $item->delete();
        $urlredirection = $this->c->router->pathFor('affichageliste', ['id'=> $l->tokenmodif]);
        return $response->withRedirect($urlredirection);

    }



}