<?php

namespace iutnc\deefy\render;
use iutnc\deefy\audio\lists\AudioList;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
class AudioListRenderer implements Renderer{
    Private AudioList $liste;

    public function __construct(AudioList $list){
        $this->list = $list;
    }

    public function render(int $selector =0): string{
        $html = "<div class='audiolist'>";
        $html .= "<h1>{$this->list->__get('nom')}</h1>";

        foreach ($this->list->__get('liste') as $track) {

            if ($track instanceof AlbumTrack) {
                $renderer = new AlbumTrackRenderer($track);
            }elseif ($track instanceof PodcastTrack) {
                $renderer = new PodcastRenderer($track);
            }
            $html .= $renderer->render(Renderer::COMPACT);
        }

        $html .= "<p><b>Total : {$this->list->__get('nbpistes')} pistes, {$this->list->__get('duree')} sec</b></p>";
        $html .= "</div>";
        return $html;
    }
}