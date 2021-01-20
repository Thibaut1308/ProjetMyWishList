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
        if(isset($this->data[0]))
        {
            $html = "<script>window.alert('La description et le titre doivent être complétés')</script>";
        }else
        {
            $html = "";
        }
        $action = $this->container->router->pathFor('new');
        $html .= <<<END
<form method="POST" action="$action">
	<label>Titre:<br> <input type="text" name="titre"/></label><br>
	<label>Description: <br><input type="text" name="description"/></label><br>
	<label>Date d'expiration: <br><input type="date" name="expiration" placeholder="YYYY-MM-JJ"/></label><br>
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
        $action3 = $this->container->router->pathFor('modifimage');
        $action4 = $this->container->router->pathFor('suppItem');

        if(!isset($_COOKIE['createur'])) {
            if($l->expiration > date('Y-m-j')) {
                setcookie('createur'.$l->no, serialize($l->no), time()+3600*24*30*12);
            }
        }else
        {
            if($l->expiration < date('Y-m-j')) {
                unset($_COOKIE['createur'.$l->no]);
            }
        }
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
            if(isset($_COOKIE['createur'.$l->no])) {
                if(is_null($val->reservation)) {
                    $reserver = "Non réservé ";

                }else{
                    $reserver = "Réservé";
                }
            }else
            {
                if(is_null($val->reservation)) {
                    $reserver = "Non réservé <form method='GET' action = $action4 ><input type='hidden' name='id' value='$val->id' /><button type='submit'>Supprimer item</button></form>";
                }else{
                    $reserver = 'Réservé par '.$val->reservation.' Message: '.$val->message;
                }
            }
            $retour .= '<li>'.$val->id.' '.$val->nom.' '.$val->desc.' '.$val->tarif.'€ '.$reserver.'</li>';
        }
        $retour .= "</ul>";
        if($l->public == 1) {
            $public = "Liste publique";
        }else
        {
            $public = "Liste privée";
        }
        $retour .= <<<END
<form method="POST" action="$action" id="formmodif">
    <input type="hidden" name="id" value="$l->no" />
	<label>Nouveau nom:<br> <input type="text" name="nom" value="$l->titre"/></label><br>
	<label>Nouvelle Description: <br><input type="text" name="description" value="$l->description"/></label><br>
	<label>Changer l'état de la liste: <input type="checkbox" name="public" value="1"> (Etat actuel: $public) </label><br>
	<button type="submit">Enregistrer la liste</button>
</form>	

<form method="POST" action="$action2" id="formitem">
    <h3>Ajouter/Modifier un item</h3>
    <label>En cas de modification, entrez le numéro de l'item à modifier (l'item doit appartenir à votre liste)<br></label>
    <input type="hidden" name="id" value="$l->no" />
    <label>Identifiant item:<br> <input type="number" name="iditem"/></label><br>
    <label>Nom:<br> <input type="text" name="nomitem"/></label><br>
    <label>Description:<br> <input type="text" name="description"/></label><br>
    <label>Url site:<br> <input type="text" name="urlitem"/></label><br>
    <label>Tarif:<br> <input type="number" name="tarifitem"/></label><br>
    <label>Image:<br> <input type="text" name="urlimgitem"/></label><br> 
    <button type="submit">Enregistrer item</button>
</form>
<form method="POST" action="$action3">
    <h3>Modifier/Supprimer une image d'un item</h3>
    <label>Identifiant item:<br> <input type="number" name="iditem"/></label><br>
    <label>Image:<br> <input type="text" name="imgitem"/></label><br>
    <label>Url site:<br> <input type="text" name="urlitem"/></label><br> 
    <button type="submit">Modifier une image</button>
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
        $liencompte = $this->htmlvars['compte'];
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