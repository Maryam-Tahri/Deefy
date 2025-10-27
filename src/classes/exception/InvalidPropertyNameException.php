<?php
namespace iutnc\deefy\exception;
use Exception;
class InvalidPropertyNameException extends Exception{
    function __construct(String $nom){
        parent::__construct("Propriété inconnue : $nom");
    }
}