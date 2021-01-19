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
        $action = $this->container->router->pathFor('reserver');
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
                      Tarif: $it->tarif<br \>
                      Image: <img src='$it->img' alt='$it->descr' witdh='200' height='200'> </p>";
            if(isset($_COOKIE['nom'])) {
                $valeur  = $_COOKIE['nom'];
            }else
            {
                $valeur = "";
            }
            if(isset($_POST['message'])) {
                $valeur2  = $_POST['message'];
            }else
            {
                $valeur2 = "";
            }
            if((is_null($it->reservation))||(is_null($it->message)))
            {

                $retour .= "Réservez l'item ?";
                $retour .= <<<END
<form method="POST" action="$action" id="formreserver">
    <input type="hidden" name="id" value="$it->id" />
    <label>Entrez votre nom: <br> <input type="text" name="nom" placeholder="nom" value="$valeur"/></label><br>
    <label>Entrez votre message: <br> <input type="text" name="message" placeholder="message" value="$valeur2"/></label><br>
    <button type="submit">Réserver</button>
</form>
END;

            }else
            {
                $retour .= "Item réservé par $it->reservation $it->message</p>";
            }
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
<p>Voici l'application MyWishList, pour afficher une liste publique cliquer sur "Affichage"
sinon créez une liste en cliquant sur "Creation".</p>
<p>Une fois la liste créée vous pouvez partager le lien "participants" pour que ces derniers puissent réserver des items.</p>
<p>Connectez-vous en cliquant sur "Espace personnel" pour retrouver plus facilement vos listes.</p>
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
            if(isset($_COOKIE['createur'.$l->no])) {
                if(is_null($val->reservation)) {
                    $reserver = "Non réservé";
                }else{
                    $reserver = "Réservé";
                }
            }else
            {
                if(is_null($val->reservation)) {
                    $reserver = "Non réservé";
                }else{
                    $reserver = 'Réservé par '.$val->reservation.' Message: '.$val->message;
                }
            }
            $lienitem = $this->container->router->pathFor('afficheritem', ['id'=>$val->id]);
            $retour .= '<li><a href="'.$lienitem.'">'.$val->id.'  '.$val->nom.' </a> '.$reserver.' </li>';
        }
        $retour .= "</ul>";
        return $retour;

    }

    private function htmlMenu()
    {
        $retour =  <<<END
<h1>Listes publiques</h1>
END;
        foreach($this->data[0] as $var=>$val) {

            $redirection = $this->container->router->pathFor('affichageliste', ['id'=>$val->token]);
            $retour .= '<p>-<a href="'.$redirection.'">'. $val->titre.'</a></p>';
        }
        return $retour;

    }

    public function render($s, $htmlvars) {
        $this->selecteur = $s;
        $this->htmlvars = $htmlvars;
        $footer = $this->htmlFooter();
        $csspath = $this->htmlvars['basepath'].'/web/css/styleaccueil.css';
        $lienaccueil = $this->htmlvars['home'];
        $lienaffichage = $this->htmlvars['affichage'];
        $liencreation = $this->htmlvars['creation'];
        $liencompte = $this->htmlvars['compte'];
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
                <li class=""><a href=$liencompte>Espace personnel</a></li>
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