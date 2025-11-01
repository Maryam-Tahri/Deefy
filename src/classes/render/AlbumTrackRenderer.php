<?php
namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\repository\DeefyRepository;
use PDO;
class AlbumTrackRenderer extends AudioTrackRenderer{
    
    public function __construct(AlbumTrack $track){
        $this->track = $track;
    }

    public function renderCompact(): string {
        $stmt = DeefyRepository::getInstance()->getPDO()->prepare("SELECT no_piste_dans_liste FROM playlist2track WHERE id_pl = :id_pl AND id_track = :id_track");
        $playlist = unserialize($_SESSION['playlist']);
        $stmt->bindValue(':id_pl', $playlist->id, PDO::PARAM_INT);
        $stmt->bindValue(':id_track', $this->track->getNumero(), PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $noPiste = $row['no_piste_dans_liste'];
        return "<p><b>{$noPiste}. {$this->track->getTitre()}</b> - {$this->track->getArtiste()}</p>
                <audio controls src='{$this->track->nomFichier}'></audio>";
    }

    public function renderLong(): string {
        return "<div>
                    <h2>{$this->track->__get('titre')}</h2>
                    <p>Artiste : {$this->track->__get('artiste')}</p>
                    <p>Album : {$this->track->__get('album')} ({$this->track->__get('annee')})</p>
                    <p>Genre : {$this->track->__get('genre')}</p>
                    <p>Durée : {$this->track->__get('duree')} sec</p>
                    <audio controls src='{$this->track->__get('nomFichier')}'></audio>
                </div>";
    }
    
}