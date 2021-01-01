<?php


namespace mywishlist\vue;


class VueCreateur
{
    private $data;
    private $selecteur;
    private $htmlvars;
    private $container;
    const FORM_LIST = 1;
    const LIST_VIEW = 2;

    public function __construct(array $data, $c)
    {
        $this->data = $data;
        $this->container = $c;
    }

    public function formListes() {
        $action = $this->container->router->pathFor('new');
        $html = <<<END
<form method="POST" action="$action">
	<label>Titre:<br> <input type="text" name="titre"/></label><br>
	<label>Description: <br><input type="text" name="description"/></label><br>
	<label>Date d'expiration: <br><input type="date" name="expiration"/></label><br>
	<button type="submit">Enregistrer la liste</button>
</form>	
END;

        return $html;
    }

    /**
     * Affichage du formulaire de modification d'une liste.
     * @return string
     */
    private function getListe() {
        $l = $this->data[0];
        $action = $this->container->router->pathFor('modifliste');
        $action2 = $this->container->router->pathFor('modifitem');
        if(!is_null($l->token))
        {
            $lienconsultation = $this->container->router->pathFor('affichageliste', ['id'=>$l->token]);
            $participant = "<p>Lien de consultation pour les participants: <a href=".$lienconsultation.">ici</a></p>";
        }else
        {
            $actionvalider = $this->container->router->pathFor('validerliste');
            $participant = <<<END
<form method="post" action="$actionvalider" id="formvalider">
    <input type="hidden" name="id" value="$l->no" />
    <button type="submit">Valider la liste</button>
</form>
END;

        }
        if(is_null($l))
        {
            return "<h2>Liste Inexistante</h2>";
        }
        $retour = <<<END
<h2>Liste n°$l->no</h2>
<h3>Espace Createur</h3>
<p>Nom: $l->titre</p>
<p>Description: $l->description</p>
<p>Expiration: $l->expiration</p>
<p>Propriétaire: $l->user_id</p>
<ul>
    <p>Items: </p>
END;
        $items = $l->items;
        foreach($items as $var=>$val)
        {
            $retour .= '<li>'.$val->id.' '.$val->nom.' '.$val->desc.' '.$val->tarif.' </li>';
        }
        $retour .= "</ul>";
        $retour .= <<<END
<form method="POST" action="$action" id="formmodif">
    <input type="hidden" name="id" value="$l->no" />
	<label>Nouveau nom:<br> <input type="text" name="nom"/></label><br>
	<label>Nouvelle Description: <br><input type="text" name="description"/></label><br>
	<button type="submit">Enregistrer la liste</button>
</form>	
<form method="POST" action="$action2" id="formitem">
    <h3>Ajouter/Modifier un item</h3>
    <input type="hidden" name="id" value="$l->no" />
    <label>Identifiant item:<br> <input type="number" name="iditem"/></label><br>
    <label>Nom:<br> <input type="text" name="nomitem"/></label><br>
    <label>Description:<br> <input type="text" name="description"/></label><br>
    <label>Url site:<br> <input type="text" name="urlitem"/></label><br>
    <label>Tarif:<br> <input type="number" name="tarifitem"/></label><br>
    <button type="submit">Enregistrer item</button>
</form>
$participant

END;

        return $retour;
    }

    public function render($s, $htmlvars) {
        $this->selecteur = $s;
        $this->htmlvars = $htmlvars;
        $footer = $this->htmlFooter();
        $csspath = $this->htmlvars['basepath'].'/web/css/stylecreateur.css';
        $lienaccueil = $this->htmlvars['home'];
        $lienaffichage = $this->htmlvars['affichage'];
        $liencreation = $this->htmlvars['creation'];
        switch ($this->selecteur) {
            case self::FORM_LIST: {
                $content = $this->formListes();
                break;
            }

            case self::LIST_VIEW: {
                $content = $this->getListe();
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
                <li class="boutoncreation"><a href="$liencreation">Créations</a> </li>
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


    public function htmlFooter() {
        $retour = file_get_contents(__DIR__.'/../../web/html/footer.html');
        return $retour;
    }
}