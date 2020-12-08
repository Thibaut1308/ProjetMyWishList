<?php


require_once 'vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as DB;
use mywishlist\models\Item;
use mywishlist\models\Liste;

$db = new DB();
$config= parse_ini_file("src/conf/conf.ini");
$db->addConnection($config);

$db->setAsGlobal();
$db->bootEloquent();

$i = Item::all();
$l = Liste::all();

echo 'Requete n°1'.'<br>';
foreach ($i as $item)
{
    echo $item->id.' '.$item->liste_id.' '.$item->nom.' '.$item->desc.' '.$item->img.' appartient à la liste de titre:  ';
    if($item->liste_id != 0)
    {
        echo $item->liste->titre.' <br>';
    }else
    {
        echo 'Il n\'y a pas de liste pour cette item'.' <br>';
    }
}
echo "<br><br>";
echo 'Requete n°2'.'<br>';
foreach ($l as $liste)
{
    echo $liste->no.' '.$liste->user_id.' '.$liste->titre.' '.$liste->description.' '.$liste->expiration.' '.$liste->token.' <br>';
}

echo "<br><br>";
echo 'Requete n°3'.'<br>';

    if(array_key_exists('id', $_GET))
    {
        $item = Item::where('id', '=', $_GET['id'])->first();
        if(!is_null($item))
        {
            echo $item->id.' '.$item->liste_id.' '.$item->nom.' '.$item->desc.' '.$item->img.' <br>';
        }
    }

echo "<br><br>";
echo 'Requete n°4'.'<br>';

if(array_key_exists('id_liste', $_GET))
{
    $l = Liste::where('no', '=', $_GET['id_liste'])->first();
    $tab = $l->items; //Abréviation de $l->items()->get()

    foreach ($tab as $var=>$val)
    {
        echo $val->id.'  '.$val->liste_id.'  '.$val->nom.'  '.$val->nom.'  '.$val->desc.'  '.$val->img.'  '.$val->url.'  '.$val->tarif.' <br>';
    }
}

/**echo "<br><br>";
echo 'Requete n°5'.'<br>';

$i = Item::where('id', '=', 5)->first();
echo "Item: <br>";
echo $i->id.'  '.$i->liste_id.'  '.$i->nom.'  '.$i->nom.'  '.$i->desc.'  '.$i->img.'  '.$i->url.'  '.$i->tarif.' <br>';
echo "Correspond à la liste: ";
echo ($i->liste->titre)."<br>";*/

/**$item = Item::where('id', '=', 28)->first();
$item->delete();
$item = Item::where('id', '=', 29)->first();
$item->delete();*/
/**
$item = new Item();
$item->liste_id = 1;
$item->nom = 'UnItem';
$item->descr = 'Un item à insérer';
$item->save();*/
