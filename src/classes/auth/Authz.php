<?php

namespace iutnc\deefy\auth;

use iutnc\deefy\exception\AuthzException;
use iutnc\deefy\repository\DeefyRepository;

class Authz {

    public static function checkRole(int $expectedRole): void {
        $user = AuthnProvider::getSignedInUser();
        
        if ($user['role'] !== $expectedRole) {
            throw new AuthzException("Accès refusé : rôle insuffisant");
        }
    }

    public static function checkPlaylistOwner(int $playlistId): void {
        $user = AuthnProvider::getSignedInUser();
        
        if ($user['role'] === 100) {
            return;
        }
        
        $config = parse_ini_file(__DIR__ . '/../../../config/deefy.db.ini');
        $pdo = new \PDO($config['dsn'], $config['user'], $config['pass']);
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user2playlist WHERE id_user = :userId AND id_pl = :playlistId");
        $stmt->execute([
            'userId' => $user['id'],
            'playlistId' => $playlistId
        ]);
        
        if ($stmt->fetchColumn() == 0) {
            throw new AuthzException("Accès refusé : vous n'êtes pas propriétaire de cette playlist");
        }
    }
}
