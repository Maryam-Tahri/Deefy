<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\listes\AudioList;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\auth\AuthzProvider;
use iutnc\deefy\exception\AuthException;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;
use PDO;

class DisplayPlaylistAction extends Action
{
    public function execute(): String {

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $bdd = DeefyRepository::getInstance()->getPDO();
            $stmt = $bdd->prepare("SELECT id_pl,nom FROM user2playlist INNER JOIN playlist ON playlist.id=user2playlist.id_pl");
            $stmt->execute();
            $html = <<<HTML
<form method='post' action='?action=display-playlist'>
<select name='playlist' required>
HTML;
            while($row  = $stmt->fetch(PDO::FETCH_ASSOC)){
                $own = AuthzProvider::checkPlaylistOwner($row['id_pl']);
                if ($own){
                $html .= <<<HTML
<option value='{$row['id_pl']}'>{$row['nom']}</option>
HTML;
                }
            }
            $html .= <<<HTML
</select>
<input type='submit' value="Choisir">
HTML;
            return $html;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $repo = DeefyRepository::getInstance();
            $own = AuthzProvider::checkPlaylistOwner($_POST['playlist']);
            if ($own){
                $playlist = $repo->findPlaylistById($_POST['playlist']);
                $rendererList = new AudioListRenderer($playlist);
                $html="<div>".$rendererList->render()."</div>";
                $_SESSION['playlist'] = serialize($playlist);
                return $html;
            }
            return <<<HTML
            <div>Vous n'avez pas accès à cette playlist </div>
            HTML;
        }

        return "<p>Méthode HTTP non supportée.</p>";
    }
}