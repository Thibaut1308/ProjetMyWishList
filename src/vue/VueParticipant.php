<?php

namespace mywishlist\vue;
use mywishlist\models\Item;

class VueParticipant
{
    private $data;
    private $selecteur;
    private $htmlvars;
    private $container;
    const LIST_VIEW = 2;
    const ITEM_VIEW = 3;
    const HOME_VIEW = 0;
    const PRINT_VIEW = 1;

    public function __construct(array $data, $c)
    {
        $this->data = $data;
        $this->container = $c;
    }

    /**
     * Méthode de génération de la vue d'un item avec réservation possible si la liste est validée.
     * @return string
     */
    public function htmlUnItem() {
        $it = $this->data[0];
        $listeitem = $this->data[1];
        if(is_null($listeitem->token))
        {
            $retour = "<h1>Affichage impossible</h1><br \>";
            $retour .= "<p>La liste de l'item doit d'abord être validée</p>";
        }else {
            $retour = "<h1>Détail de l'item n°$it->id</h1><br \>";
            $retour .= "<p>
                      Nom: $it->nom<br \>
                      Description: $it->descr<br \>
                      Liste: $it->liste_id<br \>
                      Tarif: $it->tarif</p>";
        }
        return $retour;
    }

    public function htmlFooter() {
        $retour = file_get_contents(__DIR__.'/../../web/html/footer.html');
        return $retour;
    }

    /**
     * Affiche l'accueil
     * @return string
     */
    public function htmlAccueil() {
        $retour = <<<END
<h1>Application MyWishList</h1>
<p>Voici l'application MyWishList, pour afficher une liste ou item cliquer sur "Affichage"
sinon créez une liste en cliquant sur "Creation"</p>
END;
        return $retour;

    }

    private function htmlListItem() {
        $l = $this->data[0];
        if(is_null($l))
        {
            return "<h2>Liste Inexistante</h2>";
        }
        $retour = <<<END
<h2>Liste n°$l->no</h2>
<p>Nom: $l->titre</p>
<p>Description: $l->description</p>
<p>Expiration: $l->expiration</p>
<p>Propriétaire: $l->user_id</p>
<p>Items: </p>
<ul>
END;
        $items = $l->items;
        foreach($items as $var=>$val)
        {
            $lienitem = $this->container->router->pathFor('afficheritem', ['id'=>$val->id]);
            $retour .= '<li><a href="'.$lienitem.'">'.$val->id.'  '.$val->nom.' </a></li>';
        }
        $retour .= "</ul>";
        return $retour;

    }

    private function htmlMenu()
    {
        return <<<END
<h1>Menu Affichage</h1>
<p>Ceci est le menu affichage avec les liens </p>
END;

    }

    public function render($s, $htmlvars) {
        $this->selecteur = $s;
        $this->htmlvars = $htmlvars;
        $footer = $this->htmlFooter();
        $csspath = $this->htmlvars['basepath'].'/web/css/styleaccueil.css';
        $lienaccueil = $this->htmlvars['home'];
        $lienaffichage = $this->htmlvars['affichage'];
        $liencreation = $this->htmlvars['creation'];
        switch ($this->selecteur) {
            case self::LIST_VIEW : {
                $content = $this->htmlListItem();
                break;
            }
            case self::ITEM_VIEW : {
                $content = $this->htmlUnItem();
                break;
            }
            case self::HOME_VIEW : {
                $content = $this->htmlAccueil();
                break;
            }
            case self::PRINT_VIEW : {
                $content = $this->htmlMenu();
                break;
            }
        }
        $html = <<<END
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MyWishList</title>
    <link rel="stylesheet" href=$csspath>
</head>
<body>
    <header>
        <div id="titre_principal">
            <h1>MyWishList</h1>
        </div>
        <!--Menu de navigation-->
        <nav id="menu">
            <ul>
                <li class="boutonaccueil"><a href=$lienaccueil >Accueil</a></li>
                <li class="boutoncreation"><a href=$liencreation>Créations</a> </li>
                <li class=""><a href="#">Connexion</a></li>
                <li class="boutonaff"><a href=$lienaffichage>Affichage</a></li>
            </ul>
        </nav>
    </header>
<div id="contenu">
 $content
</div>
$footer
END ;
        return $html;
    }


}