<?php
namespace iutnc\deefy\render ;


use iutnc\deefy\audio\tracks\AudioTrack;
abstract class AudioTrackRenderer implements Renderer {
    protected AudioTrack $track;

    /**
     * @return AudioTrack
     */
    public function getTrack(): AudioTrack
    {
        return $this->track;
    }

    public function __construct(AudioTrack $track) {
        $this->track = $track;
    }

    public function render(int $selector): string {
        switch ($selector) {
            case Renderer::COMPACT:
                return $this->renderCompact();
            case Renderer::LONG:
                return $this->renderLong();
            default:
                return "<p>Mode inconnu</p>";
        }
    }

    abstract public function renderCompact(): string;
    abstract public function renderLong(): string;
}
