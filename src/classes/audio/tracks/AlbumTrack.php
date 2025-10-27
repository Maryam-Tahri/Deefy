<?php
namespace iutnc\deefy\audio\tracks;


class AlbumTrack extends AudioTrack{
    private string $artiste;
    private string $album;
    private int $numero;
    private int $annee;

    public function __get(string $nom) {
        switch ($nom) {
            case 'artiste':
                return $this->artiste;
            case 'album':
                return $this->album;
            case 'numero':
                return $this->numero;
            case 'annee':
                return $this->annee;
            default:
                return parent::__get($nom);
        }
    }
    /**
     * @return string
     */
    public function getAlbum(): string
    {
        return $this->album;
    }

    /**
     * @return string
     */
    public function getArtiste(): string
    {
        return $this->artiste;
    }

    /**
     * @return int
     */
    public function getAnnee(): int
    {
        return $this->annee;
    }

    /**
     * @return int
     */
    public function getNumero(): int
    {
        return $this->numero;
    }

    public function __construct(string $titre, string $nomFichier, string $album, int $numero, string $artiste, int $annee, string $genre, int $duree) {
        parent::__construct($titre, $nomFichier, $genre, $duree);
        $this->album = $album;
        $this->numero = $numero;
        $this->artiste = $artiste;
        $this->annee = $annee;
    }

    public function __toString(): string{
        return json_encode(get_object_vars($this));
    }

}