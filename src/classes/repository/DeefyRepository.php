<?php

namespace iutnc\deefy\repository;

use iutnc\deefy\audio\lists\Playlist;
use PDO;
use Exception;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\audio\tracks\AlbumTrack;

class DeefyRepository
{
    private PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf) {
        $this->pdo = new PDO($conf['dsn'], $conf['user'], $conf['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public static function getInstance(): DeefyRepository {
        if (self::$instance === null) {
            if (empty(self::$config)) {
                throw new Exception("Configuration non définie ! Appelle d'abord DeefyRepository::setConfig().");
            }
            self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;
    }

    public static function setConfig(string $file) {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new Exception("Error reading configuration file");
        }
        $dsn = "{$conf['driver']}:host={$conf['host']};dbname={$conf['database']}";
        self::$config = ['dsn'=> $dsn, 'user'=> $conf['username'], 'pass'=> $conf['password']];
    }

    public function getPDO(): PDO {
        return $this->pdo;
    }

    public function findAllPlaylists(): array {
        $sql = "SELECT id, nom FROM playlist";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $playlists = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $playlist = new Playlist($row['nom']);
            $playlist->id = $row['id'];
            $playlists[] = $playlist;
        }
        return $playlists;
    }

    public function saveEmptyPlaylist(Playlist $playlist): Playlist {
        $sql = "INSERT INTO playlist (nom) VALUES (:nom)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nom', $playlist->__get('nom'));
        $stmt->execute();
        $playlist->id = (int)$this->pdo->lastInsertId();
        return $playlist;
    }

    public function savePodcastTrack(PodcastTrack $track): PodcastTrack {
        $sql = "INSERT INTO track (titre, auteur_podcast, duree, filename, genre) 
            VALUES (:titre, :auteur_podcast, :duree, :fichier, :genre)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':titre', $track->__get('titre'));
        $stmt->bindValue(':auteur_podcast', $track->__get('auteur')); // le placeholder :auteur correspond
        $stmt->bindValue(':duree', $track->__get('duree'));
        $stmt->bindValue(':fichier', $track->getNomFichier());
        $stmt->bindValue(':genre', $track->__get('genre'));
        $stmt->execute();

        $track->id = (int)$this->pdo->lastInsertId(); // ajoute un id à l'objet
        return $track;
    }


    public function addTrackToPlaylist(int $trackId, int $playlistId): void {
        // 1. Determine the next available track number for this playlist
        // COALESCE(MAX(...), 0) + 1 handles the case where the playlist is currently empty.
        $sql_max = "SELECT COALESCE(MAX(no_piste_dans_liste), 0) + 1 
                FROM playlist2track 
                WHERE id_pl = :id_pl";
        $stmt_max = $this->pdo->prepare($sql_max);
        $stmt_max->bindValue(':id_pl', $playlistId);
        $stmt_max->execute();
        $nextTrackNumber = $stmt_max->fetchColumn();

        // 2. Execute the correct INSERT statement
        $sql = "INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) 
            VALUES (:id_pl, :id_track, :no_piste_dans_liste)";

        $stmt = $this->pdo->prepare($sql);

        // Correct binding: $playlistId goes to :id_pl
        $stmt->bindValue(':id_pl', $playlistId, PDO::PARAM_INT);
        // $trackId goes to :id_track
        $stmt->bindValue(':id_track', $trackId, PDO::PARAM_INT);
        // Calculated number goes to :no_piste_dans_liste
        $stmt->bindValue(':no_piste_dans_liste', $nextTrackNumber, PDO::PARAM_INT);

        $stmt->execute();
    }

    public function findPlaylistById(int $playlistId): Playlist {
        $bdd = $this->pdo;
        $pl = $bdd->prepare("SELECT id,nom FROM playlist WHERE id = :id");
        $pl->bindValue(':id', $playlistId);
        $pl->execute();
        $row = $pl->fetch(PDO::FETCH_ASSOC);
        $playlist = new Playlist($row['nom']);
        $tracks = $bdd->prepare("SELECT titre,genre,duree,filename,type,artiste_album,titre_album,annee_album,numero_album,auteur_podcast,date_posdcast 
                FROM track 
                INNER JOIN playlist2track ON playlist2track.id_track=track.id 
                WHERE id_pl =:id ORDER BY playlist2track.no_piste_dans_liste;");
        $tracks->bindValue(':id', $playlistId);
        $tracks->execute();
        while ($row = $tracks->fetch(PDO::FETCH_ASSOC)){
            if($row['type'] == 'A'){
                $track = new AlbumTrack($row['titre']??'inconnu', $row['filename']??'inconnu',$row['titre_album']??'inconnu', $row['numero_album']??'inconnu',$row['artiste_album']??'inconnu', $row['annee_album']??'inconnu', $row['genre']??'inconnu', $row['duree']??'inconnu');
            }
            elseif($row['type'] == 'P'){
                $track = new PodcastTrack($row['titre']??'inconnu', $row['filename']??'inconnu',$row['auteur_podcast']??'inconnu',$row['annee_album']??'inconnu',$row['genre']??'inconnu',$row['duree']??'inconnu');
            }
            $playlist->ajouterPiste($track);
        }
        return $playlist;

    }
}
