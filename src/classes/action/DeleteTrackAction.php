<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthzProvider;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;
use PDO;

class DeleteTrackAction
{
    public function execute(): String
    {
        if (!isset($_SESSION['user'])) {
            return <<<HTML
<div>Merci de vous connecter</div>
HTML;

        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $bdd = DeefyRepository::getInstance()->getPDO();
            $stmt = $bdd->prepare("SELECT track.id,titre,artiste_album FROM playlist2track " .
                "INNER JOIN track ON track.id=playlist2track.id_track WHERE playlist2track.id_pl = :id");
            $playlist = unserialize($_SESSION['playlist']);
            $stmt->bindParam(':id', $playlist->id);
            $stmt->execute();
            $html = <<<HTML
            <form method='post' action='?action=del-track'>
            <select name='track' required>
            HTML;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $html .= <<<HTML
                <option value='{$row['id']}'>{$row['titre']} - {$row['artiste_album']}</option>
                HTML;
            }
            $html .= <<<HTML
            </select>
            <input type='submit' value="Choisir">
            HTML;
        return $html;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $repo = DeefyRepository::getInstance();
            $playlist = unserialize($_SESSION['playlist']);
            $res = $repo->delTrackFromPlaylist($playlist->id,$_POST['track']);
            if ($res){
                return <<<HTML
                <div>Piste bien retirée de la playlist</div>
                <a href='?action=display-playlist'>Voir mes playlists</a>
                HTML;
            }else{
                return <<<HTML
<div>Erreur lors de la suppression de piste de la playlist</div>
HTML;

            }

        }

        return "<p>Méthode HTTP non supportée.</p>";
    }
}