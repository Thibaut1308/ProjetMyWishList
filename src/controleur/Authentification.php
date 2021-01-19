<?php


namespace mywishlist\controleur;


use mywishlist\models\Utilisateur;

/**
 * Class Authentification
 * @package mywishlist\controleur
 */
class Authentification
{

    /**
     * Création d'utilisateur
     * @param $username pseudo de l'utilisateur
     * @param $mdp mot de passe de l'utilisation
     */
    public static function createUser($username, $mdp){

        $hash = password_hash($mdp, PASSWORD_DEFAULT);

        $user = new Utilisateur();
        $user->nomutilisateur = $username;
        $user->motdepasse = $hash;
        $user->roleiD = 1;
        $user->save();
    }

    /**
     * Methode d'authentification
     * @param $username pseudo de l'utilisateur
     * @param $mdp mot de passe de l'utilisation
     * @return boolean
     */
    public static function authenticate( $username, $mdp) {
        $user = Utilisateur::where('nomutilisateur', '=', $username)->first();
        if (is_null($user)) {
            return false;
        }
        if (password_verify($mdp, $user->motdepasse)) {
            self::loadProfile($user->uid);
            return true;
        }else
        {
            return false;
        }
    }

    /**
     * Methode qui charge un profile utilisateur dans une variable de session
     * @param $uid id de l'utilisateur
     */
    private static function loadProfile($uid){

        $user = Utilisateur::find($uid);
        $_SESSION['profile'] = array(
            'id' => $user->uid,
            'username'   => $user->nomutilisateur,
            'role_id'    => $user->roleiD,
            'client_ip'  => $_SERVER['REMOTE_ADDR']
        );
    }

    /**
     * Methode de Controle des droits d'accès
     * @param $required niveau requis
     * @return boolean
     */
    public static function checkAccessRights($required){
        if ($_SESSION['profile']['role_id'] < $required) {
            return false ;
        }else
        {
            return true;
        }
    }


}