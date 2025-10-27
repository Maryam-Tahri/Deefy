<?php

namespace iutnc\deefy\audio\tracks;

use AudioList;
use InvalidPropertyValueException;

class Album extends AudioList
{
    private string $artiste;
    private string $date;

    public function __construct(string $artiste, string $date, string $titre, array $liste)
    {
        if (empty(liste)) {
            throw new InvalidPropertyValueException("un album doit contenir au moins une piste", "liste");
        } else {
            $this->artiste = $artiste;
            $this->date = $date;
            parent::__construct($titre, $liste);
        }

    }

    public function setArtiste(string $artiste): void
    {
        $this->artiste = $artiste;
    }

    public function setDateSortie(string $dateSortie): void
    {
        $this->date = $dateSortie;
    }
}