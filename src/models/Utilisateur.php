<?php


namespace mywishlist\models;
use Illuminate\Database\Eloquent\Model as Modele;

class Utilisateur extends Modele
{
    protected $table = 'utilisateur';
    protected $primaryKey = 'uid';
    public $timestamps = false;
}

