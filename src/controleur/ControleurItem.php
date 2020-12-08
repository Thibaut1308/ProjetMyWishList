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
        $rs->getBody()->write($vue->render(3));
        return $rs;
    }
}