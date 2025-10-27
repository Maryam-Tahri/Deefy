<?php
namespace iutnc\deefy\audio\tracks;
class PodcastTrack extends AudioTrack{
    private string $auteur;
    private string $date;

    function __get($nom):mixed{
        switch ($nom) {
            case 'auteur':
                return $this->auteur;
            case 'date':
                return $this->date;
            default:
                return parent::__get($nom);
        }
    }


    public function __construct(string $titre, string $nomFichier, string $auteur, string $date, string $genre, int $duree) {
        parent::__construct($titre, $nomFichier, $genre, $duree);
        $this->auteur = $auteur;
        $this->date = $date;
    }
}