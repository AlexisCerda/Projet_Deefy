<?php

namespace iutnc\deefy\repository;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\audio\tracks\AudioTrack;

class DeefyRepository {
    private \PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf) {
        $dsn = $conf['dsn'] ?? 'mysql:host=localhost;dbname=deefy';
        $user = $conf['user'] ?? 'root';
        $pass = $conf['pass'] ?? '';
        
        $this->pdo = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ]);
    }

    public static function setConfig(string $file): void {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("Erreur lors de la lecture du fichier de configuration");
        }
        self::$config = $conf;
    }

    public static function getInstance(): DeefyRepository {
        if (is_null(self::$instance)) {
            self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;
    }

    public function findPlaylistById(int $id): ?Playlist {
        $stmt = $this->pdo->prepare("SELECT id, nom FROM playlist WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }

        $pl = new Playlist($row['nom']);
        $pl->id = $row['id'];

        $stmt = $this->pdo->prepare("
            SELECT t.*, p2t.no_piste_dans_liste 
            FROM track t
            INNER JOIN playlist2track p2t ON t.id = p2t.id_track
            WHERE p2t.id_pl = ?
            ORDER BY p2t.no_piste_dans_liste
        ");
        $stmt->execute([$id]);

        while ($trackRow = $stmt->fetch()) {
            $track = $this->createTrackFromRow($trackRow);
            $pl->ajouterPiste($track);
        }

        return $pl;
    }

    private function createTrackFromRow(array $row): AudioTrack {
        if ($row['type'] === 'P') {
            $track = new PodcastTrack($row['titre'], $row['filename']);
            if (!empty($row['auteur_podcast'])) {
                $track->auteur = $row['auteur_podcast'];
            }
            if (!empty($row['date_posdcast'])) {
                $track->date = $row['date_posdcast'];
            }
        } else {
            $track = new AlbumTrack($row['titre'], $row['filename']);
            if (!empty($row['artiste_album'])) {
                $track->artiste = $row['artiste_album'];
            }
            if (!empty($row['titre_album'])) {
                $track->album = $row['titre_album'];
            }
            if (!empty($row['annee_album'])) {
                $track->annee = $row['annee_album'];
            }
            if (!empty($row['numero_album'])) {
                $track->numero = $row['numero_album'];
            }
        }
        
        $track->id = $row['id'];
        if (!empty($row['genre'])) {
            $track->genre = $row['genre'];
        }
        if (!empty($row['duree'])) {
            $track->duree = (int)$row['duree'];
        }
        
        return $track;
    }

    public function savePlaylist(Playlist $pl, int $userId): int {
        $stmt = $this->pdo->prepare("INSERT INTO playlist (nom) VALUES (?)");
        $stmt->execute([$pl->nom]);
        $playlistId = (int)$this->pdo->lastInsertId();
        
        $stmt = $this->pdo->prepare("INSERT INTO user2playlist (id_user, id_pl) VALUES (?, ?)");
        $stmt->execute([$userId, $playlistId]);
        
        return $playlistId;
    }

    public function saveTrack(AudioTrack $track): int {
        if ($track instanceof PodcastTrack) {
            $stmt = $this->pdo->prepare("
                INSERT INTO track (titre, filename, type, auteur_podcast, date_posdcast, genre, duree) 
                VALUES (?, ?, 'P', ?, ?, ?, ?)
            ");
            $stmt->execute([
                $track->titre,
                $track->nom,
                $track->auteur,
                $track->date,
                $track->genre,
                $track->duree
            ]);
        } else if ($track instanceof AlbumTrack) {
            $stmt = $this->pdo->prepare("
                INSERT INTO track (titre, filename, type, artiste_album, titre_album, annee_album, numero_album, genre, duree) 
                VALUES (?, ?, 'A', ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $track->titre,
                $track->nom,
                $track->artiste,
                $track->album,
                $track->annee,
                $track->numero,
                $track->genre,
                $track->duree
            ]);
        }
        
        return (int)$this->pdo->lastInsertId();
    }

    public function addTrackToPlaylist(int $trackId, int $playlistId, int $position): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$playlistId, $trackId, $position]);
    }

    public function findPlaylistsByUserId(int $userId): array {
        $stmt = $this->pdo->prepare("
            SELECT p.id, p.nom 
            FROM playlist p
            INNER JOIN user2playlist u2p ON p.id = u2p.id_pl
            WHERE u2p.id_user = ?
        ");
        $stmt->execute([$userId]);
        
        $playlists = [];
        while ($row = $stmt->fetch()) {
            $pl = new Playlist($row['nom']);
            $pl->id = $row['id'];
            $playlists[] = $pl;
        }
        
        return $playlists;
    }
}
