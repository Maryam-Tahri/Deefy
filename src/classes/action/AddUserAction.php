<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;

class AddUserAction extends Action
{
    public function execute(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $html = "<h2>Inscription</h2>
                <form method='post' action='?action=add-user'>
                    <label for='email'>Email :</label><br>
                    <input type='text' name='email' id='email' required><br><br>

                    <label for='mdp'>Mot de passe :</label><br>
                    <input type='password' name='mdp' id='mdp' required><br><br>               

                    <button type='submit'>Inscription</button>
                </form>";
            return $html;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
            $mdp = filter_var($_POST['mdp'] ?? '', FILTER_SANITIZE_STRING);
            $res = AuthnProvider::register($email, $mdp);
            if ($res) {
                $html = "<div>Inscription fini !</div><br><a href='?action=login'>Se connecter</a>";
                return $html;
            }else{
                $html = "<p>Erreur à l'inscription. Il faut : <li><Strong>au moins 10 caractère</Strong></li><li><Strong>au moins 1 caractère spécial</Strong></li><li><Strong>au moins une majuscule</Strong></li><li><Strong>au moins une minuscule</Strong></li></p>";
                return $html;
            }
        }

        return "<p>Méthode non supportée.</p>";

    }

}