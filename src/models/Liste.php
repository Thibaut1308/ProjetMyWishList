<?php

namespace mywishlist\models;
use \Illuminate\Database\Eloquent\Model as Modele;

class Liste extends Modele
{
    protected $table = 'liste';
    protected $primaryKey = 'no' ;
    public $timestamps = false;

    public function items() {
        return $this->hasMany('mywishlist\models\Item', 'liste_id' );
    }
}