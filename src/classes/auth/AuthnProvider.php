<?php

namespace iutnc\deefy\auth;

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\exception\AuthException;
class AuthnProvider {

    public static function signin(string $email,
                                  string $passwd2check): bool {
        $bdd = DeefyRepository::getInstance()->getPDO();
        $user = $bdd->prepare("SELECT id,passwd FROM user WHERE email = ?");
        $user->bindParam(1, $email);
        $user->execute();
        $row = $user->fetch();
        if (isset($row['passwd'])) {
            if (!password_verify($passwd2check, $row['passwd'])) {
                throw new AuthException("Auth error : invalid credentials");
            }
        }else{
            throw new AuthException("Auth error : invalid credentials");
        }
        $_SESSION['user'] = [
            'id'=>$row['id'],
            $email=>$row['passwd']
        ];
        return true;
    }

    public static function register(string $email,string $passwd): bool {
        $bdd = DeefyRepository::getInstance()->getPDO();
        $user = $bdd->prepare("SELECT email FROM User WHERE email = ?");
        $user->bindParam(1, $email);
        $user->execute();
        $row = $user->fetch();
        if ($row['email']) {
            return false;
        }
        $digit = preg_match("#[\d]#", $passwd); // au moins un digit
        $special = preg_match("#[\W]#", $passwd); // au moins un car. spécial
        $lower = preg_match("#[a-z]#", $passwd); // au moins une minuscule
        $upper = preg_match("#[A-Z]#", $passwd); // au moins une majuscule
        if (!$digit ||!$special||!$lower||!$upper||strlen($passwd) < 10) {
            return false;
        }
        $hashed = password_hash($passwd, PASSWORD_DEFAULT, ['cost' => 12]);
        $user = $bdd->prepare("INSERT INTO user (email, passwd,role) VALUES (?, ?,1)");
        $user->bindParam(1, $email);
        $user->bindParam(2, $hashed);
        $user->execute();
        return true;

    }

    public static function getSignedInUser(){
        if (isset($_SESSION['user'])) {
            return $_SESSION['user']['id'];
        }else{
            throw new AuthException("Vous n'êtes pas connecter !");
        }
    }
}