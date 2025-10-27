<?php
namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AlbumTrack;
class AlbumTrackRenderer extends AudioTrackRenderer{
    
    public function __construct(AlbumTrack $track){
        $this->track = $track;
    }

    public function renderCompact(): string {
        return "<p><b>{$this->track->getNumero()}. {$this->track->getTitre()}</b> - {$this->track->getArtiste()}</p>
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