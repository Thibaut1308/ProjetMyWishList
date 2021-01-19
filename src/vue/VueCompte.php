<?php


namespace mywishlist\vue;


use Illuminate\Support\Facades\Auth;
use mywishlist\controleur\Authentification;

class VueCompte
{
    private $data;
    private $selecteur;
    private $htmlvars;
    private $container;
    const AUTHENTIFICATION_VIEW = 1;
    const LIST_VIEW = 2;

    function __construct(array $data, $c)
    {
        $this->data = $data;
        $this->container = $c;
    }

    public function formlaireAuthentification()
    {
        $actionconnection = $this->container->router->pathFor('connection');
        $actioninscription = $this->container->router->pathFor('inscription');

        $retour = "<h2>Inscription</h2>";
        $retour .= <<<END
<form action="$actioninscription" method="post">
        <label for="name">Nom d'utilisateur :</label>
        <input type="text" id="name" name="utilisateur">
        <label for="pass">Mot de passe:</label>
        <input type="password" id="pass" name="motdepasse">
        <button type="submit">S'inscrire</button>
</form>
END;

        $retour .= "<h2>Connexion</h2>";
        $retour .= <<<END
<form action="$actionconnection" method="post">
        <label for="coname">Nom d'utilisateur :</label>
        <input type="text" id="coname" name="utilisateur">
        <label for="copass">Mot de passe:</label>
        <input type="password" id="copass" name="motdepasse">
        <button type="submit">Se connecter</button>
</form>
END;
        return $retour;
    }

    public function listesMembre() {
        $username = $_SESSION['profile']['username'];
        $action = $this->container->router->pathFor('ajouterliste');
        $retour = "<h2>Vous êtes connecté $username</h2>";
        $retour .= "<h3>Vos listes</h3>";
        $retour .= "<ul>";
        foreach ($this->data[0] as $l) {
            $lien = $this->container->router->pathFor('affichageliste', ['id'=>$l->tokenmodif]);
            $retour .= '<li><a href="'.$lien.'">'.$l->no.'  '.$l->titre.' '.$l->expiration.'</a></li>';
        }
        $retour.= "</ul>";
        $retour .= <<<END
<form action="$action" method="post">
    <label for="token"> Entrez ici un token de mofication pour ajouter une de vos listes à votre compte:</label>
    <input type="text" id="token" name="tokenmodif" placeholder="Token de mofication">
    <button type="submit">Ajouter</button>
</form>
END;

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

            case self::AUTHENTIFICATION_VIEW: {
                $content = $this->formlaireAuthentification();
                break;
            }

            case self::LIST_VIEW: {
                $content = $this->listesMembre();
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

    public function htmlFooter() {
        $retour = file_get_contents(__DIR__.'/../../web/html/footer.html');
        return $retour;
    }




}