<?php

namespace iutnc\deefy\auth;

use iutnc\deefy\exception\AuthnException;

class AuthnProvider {

    public static function signin(string $email, string $password): void {
        $config = parse_ini_file(__DIR__ . '/../../../config/deefy.db.ini');
        $pdo = new \PDO($config['dsn'], $config['user'], $config['pass']);

        $stmt = $pdo->prepare("SELECT id, passwd, role FROM user WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['passwd'])) {
            throw new AuthnException("Identifiants invalides");
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $email,
            'role' => $user['role']
        ];
    }

    public static function register(string $email, string $password): void {
        if (strlen($password) < 10) {
            throw new AuthnException("Le mot de passe doit contenir au moins 10 caractères");
        }

        $config = parse_ini_file(__DIR__ . '/../../../config/deefy.db.ini');
        $pdo = new \PDO($config['dsn'], $config['user'], $config['pass']);

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            throw new AuthnException("Un compte existe déjà avec cet email");
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO user (email, passwd, role) VALUES (:email, :passwd, 1)");
        $stmt->execute([
            'email' => $email,
            'passwd' => $hashedPassword
        ]);
    }

    public static function getSignedInUser(): array {
        if (!isset($_SESSION['user'])) {
            throw new AuthnException("Aucun utilisateur authentifié");
        }
        return $_SESSION['user'];
    }
}
