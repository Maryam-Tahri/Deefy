<?php

namespace iutnc\deefy\exception;
use Exception;
class AuthException extends Exception{
    function __construct(String $nom){
        parent::__construct(" $nom");
    }
}