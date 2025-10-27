<?php
namespace iutnc\deefy\audio\lists;
class AudioList{
    protected string $nom;
    protected int $nbpistes;
    protected int $duree = 0;
    protected array $liste;

    public function __construct(string $nom, array $liste = []){
        $this->nom = $nom;
        $this->liste = $liste;
        $this->nbpistes = count($liste);
        if($this->nbpistes > 0){
            for($i = 0; $i < $this->nbpistes; $i++){
                $this->duree += $this->liste[$i]->__get('duree');
            }
        }

    }

    public function __get($name){
        switch ($name){
            case 'nom':
                return $this->nom;
            case 'nbpistes':
                return $this->nbpistes;
            case 'duree':
                return $this->duree;
            case 'liste':
                return $this->liste;
        }
    }
}