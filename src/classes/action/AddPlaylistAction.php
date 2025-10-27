<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class AddPlaylistAction extends Action
{
    public function execute(): string
    {
        if (!isset($_SESSION['user'])) {
            return <<<HTML
<div>Merci de vous connecter</div>
HTML;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $html = "<h2>Créer une nouvelle playlist</h2>
                    <form method='post' action='?action=add-playlist'>
                        <label for='nom'>Nom de la playlist :</label>
                        <input type='text' name='nom' id='nom' required>
                        <br><br>
                        <button type='submit'>Créer</button>
                    </form>";
            return $html;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nomRaw = $_POST['nom'] ?? '';
            $nom = filter_var($nomRaw, FILTER_SANITIZE_STRING);

            if ($nom === '') {
                return "<p>Erreur : il faut entrer un nom.</p>";
            }

            $playlist = new Playlist($nom);

            $repo = DeefyRepository::getInstance();
            $playlist = $repo->saveEmptyPlaylist($playlist);

            $_SESSION['playlist'] = serialize($playlist);

            $renderer = new AudioListRenderer($playlist);
            $affichage = $renderer->render();

            $lien = '<p><a href="?action=add-track">Ajouter une piste</a></p>';

            return $affichage . "\n" . $lien;
        }

        return "<p>Méthode HTTP non supportée.</p>";
    }
}
