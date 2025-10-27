<?php

namespace iutnc\deefy\action;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthException;
class logoutAction
{
    public function execute(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            unset($_SESSION['user']);
            $html = "<div>Vous êtes bien déconnecter</div>";
            session_destroy();
            return $html;
        }
        return "<p>Méthode HTTP non supportée.</p>";
    }
}