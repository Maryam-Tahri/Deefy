<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\render\AudioListRenderer;

class AddPodcastTrackAction extends Action
{
    public function execute(): string
    {

        if (!isset($_SESSION['user'])){
            return <<<HTML
<div>Merci de vous connecter</div>
HTML;
        }

        if (!isset($_SESSION['playlist'])) {
            return "<p>Aucune playlist sélectionné . <a href='?action=display-playlist'>Choissisez-en une</a></p>";
        }


        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $html = "<h2>Ajouter une nouvelle piste audio</h2>
                <form method='post' enctype='multipart/form-data' action='?action=add-track'>
                    <label for='titre'>Titre :</label><br>
                    <input type='text' name='titre' id='titre' required><br><br>

                    <label for='album'>Album :</label><br>
                    <input type='text' name='album' id='album' required><br><br>

                    <label for='genre'>Genre :</label><br>
                    <input type='text' name='genre' id='genre' required><br><br>

                    <label for='artiste'>Artiste :</label><br>
                    <input type='text' name='artiste' id='artiste' required><br><br>

                    <label for='annee'>Année :</label><br>
                    <input type='number' name='annee' id='annee' required><br><br>

                    <label for='numero'>Numéro de piste :</label><br>
                    <input type='number' name='numero' id='numero' required><br><br>

                    <label for='duree'>Durée (secondes) :</label><br>
                    <input type='number' name='duree' id='duree' required><br><br>

                    <label for='userfile'>Fichier audio (.mp3 uniquement) :</label><br>
                    <input type='file' name='userfile' id='userfile' accept='.mp3,audio/mpeg' required><br><br>

                    <button type='submit'>Créer la piste</button>
                </form>";
            return $html;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {


            $titre = filter_var($_POST['titre'] ?? '', FILTER_SANITIZE_STRING);
            $album = filter_var($_POST['album'] ?? '', FILTER_SANITIZE_STRING);
            $genre = filter_var($_POST['genre'] ?? '', FILTER_SANITIZE_STRING);
            $artiste = filter_var($_POST['artiste'] ?? '', FILTER_SANITIZE_STRING);
            $annee = intval($_POST['annee'] ?? 0);
            $numero = intval($_POST['numero'] ?? 0);
            $duree = intval($_POST['duree'] ?? 0);

            if (empty($titre) || empty($album) || empty($genre) || empty($artiste)
                || $annee <= 0 || $numero <= 0 || $duree <= 0) {
                return "<p>Erreur : veuillez renseigner tous les champs correctement.</p>";
            }

            if (!isset($_FILES['userfile']) || $_FILES['userfile']['error'] !== UPLOAD_ERR_OK) {
                return "<p>Erreur : fichier audio manquant ou erreur lors de l'upload.</p>";
            }

            $file = $_FILES['userfile'];
            $filename = $file['name'];

            if (substr(strtolower($filename), -4) !== '.mp3') {
                return "<p>Erreur : le fichier doit être au format .mp3</p>";
            }

            if ($file['type'] !== 'audio/mpeg') {
                return "<p>Erreur : type MIME invalide</p>";
            }

            if (stripos($filename, '.php') !== false) {
                return "<p>Erreur : fichiers PHP interdits</p>";
            }

            if (!is_uploaded_file($file['tmp_name'])) {
                return "<p>Erreur : fichier non authentifié</p>";
            }

            $uploadDir = __DIR__ . '/../../../audio/';
            var_dump(__DIR__);
            $randomName = uniqid('track_', true) . '.mp3';
            $destinationPath = $uploadDir . $randomName;

            if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
                return "<p>Erreur : impossible de déplacer le fichier.</p>";
            }

            $trackPath = '../audio/' . $randomName;

            $track = new AlbumTrack(
                $titre,
                $trackPath,
                $album,
                $numero,
                $artiste,
                $annee,
                $genre,
                $duree
            );

            $playlist = unserialize($_SESSION['playlist']);
            $playlist->ajouterPiste($track);
            $_SESSION['playlist'] = serialize($playlist);


            $renderer = new AudioListRenderer($playlist);
            $affichage = $renderer->render();
            $lien = '<p><a href="?action=add-track">Ajouter encore une piste</a></p>';

            return $affichage . $lien;
        }

        return "<p>Méthode HTTP non supportée.</p>";
    }
}
