<?php

namespace iutnc\deefy\audio\tracks;
use Exception;
class AudioTrack{
    protected string $titre;
    protected string $genre;
    protected string $nomFichier;
    protected int $duree;

    public function __get(string $nom) {
        if (property_exists($this, $nom)) {
            return $this->$nom;
        } else {
            throw new \Exception("invalid property : <$nom>");
        }
    }

    /**
     * @return string
     */
    public function getNomFichier(): string
    {
        return $this->nomFichier;
    }

    /**
     * @return int
     */
    public function getDuree(): int
    {
        return $this->duree;
    }


    /**
     * @return string
     */
    public function getGenre(): string
    {
        return $this->genre;
    }


    /**
     * @return string
     */
    public function getTitre(): string
    {
        return $this->titre;
    }

    public function __construct(string $titre, string $nomFichier, string $genre, int $duree) {
        $this->titre = $titre;
        $this->nomFichier = $nomFichier;
        $this->genre = $genre;
        $this->duree = $duree;
    }

    public function __toString(): string{
        return json_encode(get_object_vars($this));
    }
}