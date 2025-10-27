<?php
namespace iutnc\deefy\exception;
use Exception;

class InvalidPropertyValueException extends Exception{
    function __construct(string $message, string $val){
        parent::__construct("Valeur invalide pour la propriété $message : $val");
    }
}