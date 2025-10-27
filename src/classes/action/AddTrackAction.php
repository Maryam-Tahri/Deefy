<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class AddTrackAction extends Action
{
    public function execute(): string
    {
        if (!isset($_SESSION['user'])) {
            return <<<HTML
<div>Merci de vous connecter</div>
HTML;
        }

        if (!isset($_SESSION['playlist'])) {
            return "<p>Aucune playlist sélectionnée. <a href='?action=display-playlist'>Choisissez-en une</a></p>";
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
            try {
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
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $randomName = uniqid('track_', true) . '.mp3';
                $destinationPath = $uploadDir . $randomName;

                if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
                    return "<p>Erreur : impossible de déplacer le fichier.</p>";
                }

                $trackPath = 'audio/' . $randomName;

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

                // DEBUG: Vérifier que la playlist a bien un ID
                if (!isset($playlist->id) || $playlist->id <= 0) {
                    return "<p>Erreur : La playlist n'a pas d'ID valide la playlist n'a pas été sauvegardée en base.</p>";
                }

                $repo = DeefyRepository::getInstance();


                $savedTrack = $repo->saveAlbumTrack($track);

                //Vérifier que la playlist a bien un ID
                if (!isset($savedTrack->id) || $savedTrack->id <= 0) {
                    return "<p>Erreur : La piste n'a pas été sauvegardée correctement </p>";
                }


                $repo->addTrackToPlaylist($savedTrack->id, $playlist->id);

                $playlist = $repo->findPlaylistById($playlist->id);
                $_SESSION['playlist'] = serialize($playlist);

                $renderer = new AudioListRenderer($playlist);
                $affichage = $renderer->render();
                $lien = '<p><a href="?action=add-track">Ajouter encore une piste</a></p>';

                $success = "<p >✓ Piste ajoutée avec succès (ID: {$savedTrack->id}, Playlist ID: {$playlist->id})</p>";

                return $success . $affichage . $lien;

            } catch (\Exception $e) {
                return "<p> Erreur lors de l'ajout de la piste </p>";
            }
        }

        return "<p>Méthode HTTP non supportée.</p>";
    }
}