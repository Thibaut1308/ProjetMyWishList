<?php


namespace mywishlist\vue;


class VueCreateur
{
    private $data;
    private $selecteur;
    private $htmlvars;
    private $container;
    const FORM_LIST = 1;

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
	<button type="submit">Enregistrer la liste</button>
</form>	
END;

        return $html;
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
            case self::FORM_LIST: {
                $content = $this->formListes();
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
 <p>$content</p>
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