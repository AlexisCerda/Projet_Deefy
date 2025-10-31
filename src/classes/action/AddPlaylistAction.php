<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\audio\lists\Playlist;

class AddPlaylistAction extends Action {

    public function __construct(){
        parent::__construct();
    }

    public function execute(): string {
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            return $this->Formulaire();
        } else {
            return $this->handlePost();
        }
    }

    public function Formulaire(string $error='', string $titre=''): string {
        $titre = htmlspecialchars($titre);
        $errorMessage = $error ? "<p>$error</p>" : '';
        return <<<HTML
<form method="post" action="?action=add-playlist">
    <label>Titre de la playlist : <input type="text" name="titre" value="{$titre}" required></label><br>
    <button type="submit">Créer la playlist</button>
</form>
$errorMessage
HTML;
    }

    public function handlePost(): string {
        try {
            $user = AuthnProvider::getSignedInUser();
            $nomPlaylist = htmlspecialchars($_POST['titre'] ?? '');
            
            $playlist = new Playlist($nomPlaylist, []);
            $repo = DeefyRepository::getInstance();
            $playlistId = $repo->savePlaylist($playlist, $user['id']);
            
            $playlist->id = $playlistId;
            $_SESSION['playlist'] = $playlist;
            
            return "<p>Playlist créée avec succès.</p><a href='?action=display-playlist&id=$playlistId'>Voir la playlist</a>";
        } catch (\Exception $e) {
            return $this->Formulaire("Erreur: {$e->getMessage()}", htmlspecialchars($_POST['titre'] ?? ''));
        }
    }
}
