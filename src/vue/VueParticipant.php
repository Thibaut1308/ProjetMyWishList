<?php

namespace mywishlist\vue;
use mywishlist\models\Item;

class VueParticipant
{
    private $i;
    private $selecteur;
    const LIST_VIEW = 2;
    const ITEM_VIEW = 3;

    public function __construct(array $item)
    {
        $this->i = $item;
    }

    public function render($s) {
        $this->selecteur = $s;
        switch ($this->selecteur) {
            case self::LIST_VIEW : {
                $content = $this->htmlListItem();
                break;
            }
            case self::ITEM_VIEW : {
                $content = $this->htmlUnItem();
                break;
            }
        }
        $html = <<<END
<!DOCTYPE html> <html>
<body> …
<div class="content">
 $content
</div>
</body><html>
END ;
        return $html;
    }

    public function htmlUnItem() {
        $it = $this->i[0];
        $retour = "<p>Nom: $it->nom<br \>
                      Description: $it->descr</p>";
        return $retour;
    }

    public function htmlListItem() {

    }
}