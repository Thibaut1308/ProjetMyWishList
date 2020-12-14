<?php

namespace mywishlist\vue;
use mywishlist\models\Item;

class VueParticipant
{
    private $data;
    private $selecteur;
    private $htmlvars;
    const LIST_VIEW = 2;
    const ITEM_VIEW = 3;
    const HOME_VIEW = 0;
    const PRINT_VIEW = 1;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function render($s, $htmlvars) {
        $this->selecteur = $s;
        $this->htmlvars = $htmlvars;
        //$accueil = $this->htmlAccueil();
        $footer = $this->htmlFooter();
        $csspath = $this->htmlvars['basepath'].'/web/css/styleaccueil.css';
        $lienaccueil = $this->htmlvars['home'];
        $lienaffichage = $this->htmlvars['affichage'];
        //'ProjetMyWishList'.'/../../web/css/styleaccueil.css'
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
                <li class="boutoncreation"><a href="#">Créations</a> </li>
                <li class="boutonaff"><a href=$lienaffichage>Affichage</a></li>
            </ul>
        </nav>
    </header>
<div id="contenu">
 <p>$content</p>
</div>
$footer
END ;
        return $html;
    }

    public function htmlUnItem() {
        $it = $this->data[0];
        $retour = "<p>Nom: $it->nom<br \>
                      Description: $it->descr</p>";
        return $retour;
    }

    public function htmlFooter() {
        $retour = file_get_contents(__DIR__.'/../../web/html/footer.html');
        return $retour;
    }

    /**public function htmlListItem() {

    }*/
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
        $retour = <<<END
<p>Liste de nom: $l->titre<p>
END;
        return $retour;

    }

    private function htmlMenu()
    {
        return <<<END
<h1>Menu Affichage</h1>
<p>Ceci est le menu affichage avec les liens </p>
END;

    }
}