<?php
namespace iutnc\deefy\render;
use iutnc\deefy\audio\tracks\PodcastTrack;
class PodcastRenderer extends AudioTrackRenderer{
    

    public function __construct(PodcastTrack $track){
        $this->track = $track;
    }

    public function renderCompact(): string {
        return "<p><b>{$this->track->__get('titre')}</b> - {$this->track->__get('auteur')}</p>
                <audio controls src='{$this->track->__get('nomFichier')}'></audio>";
    }

    public function renderLong(): string {
        return "<div>
                    <h2>{$this->track->getTitre()}</h2>
                    <p>Auteur : {$this->track->__get('auteur')}</p>
                    <p>Date : {$this->track->__get('date')}</p>
                    <p>Genre : {$this->track->__get('genre')}</p>
                    <p>Durée : {$this->track->__get('duree')} sec</p>
                    <audio controls src='{$this->track->__get('nomFichier')}'></audio>
                </div>";
    }
}