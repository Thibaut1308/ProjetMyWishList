<?php

namespace mywishlist\models;
use \Illuminate\Database\Eloquent\Model as Modele;

class Item extends Modele
{
    protected $table = 'item';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function liste() {
        return $this->belongsTo('mywishlist\models\Liste', 'liste_id');
    }
}