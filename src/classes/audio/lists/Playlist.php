<?php
namespace iutnc\deefy\audio\lists;
use iutnc\deefy\audio\lists\AudioList;
use iutnc\deefy\audio\tracks\AudioTrack;
class Playlist extends AudioList {
    public function ajouterPiste(AudioTrack $track): void {
        $this->liste[] = $track;
        $this->nbpistes++;
        $this->duree += $track->__get('duree');
    }

    public function supprimerPiste(int $index): void {
        if ($this->liste[$index] != null) {
            $this->duree -= $this->liste[$index]->__get('duree');

            $nouvelleListe = [];
            $j = 0;
            for ($i = 0; $i < $this->nbpistes; $i++) {
                if ($i !== $index) {
                    $nouvelleListe[$j] = $this->liste[$i];
                    $j++;
                }
            }

            $this->liste = $nouvelleListe;
            $this->nbpistes = count($this->liste);
        }
    }

    public function ajouterListe(array $pistes): void {
        foreach ($pistes as $piste) {
            $doublon = false;
            foreach ($this->liste as $existante) {
                if ($piste->getNomFichier() === $existante->getNomFichier()) {
                    $doublon = true;
                    break;
                }
            }
            if (!$doublon) {
                $this->ajouterPiste($piste);
            }
        }
    }
}