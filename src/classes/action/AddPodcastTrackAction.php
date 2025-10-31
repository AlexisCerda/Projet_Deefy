<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\exception\AuthzException;

class AddPodcastTrackAction extends Action {

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

    public function Formulaire(string $error='', string $titreTrack= ''): string {
        $titreTrack = htmlspecialchars($titreTrack);
        $errorMessage = $error ? "<p>$error</p>" : '';
        return <<<HTML
<form method="post" action="?action=add-track" enctype="multipart/form-data">
    <label>Titre de la piste : <input type="text" name="titre" value="{$titreTrack}" required></label><br>
    <label>Fichier audio (.mp3) : <input type="file" name="audiofile" accept=".mp3,audio/mpeg" required></label><br>
    <button type="submit">Ajouter la piste</button>
</form>
$errorMessage
HTML;
    }

    public function handlePost(): string {
        try {
            $titre = htmlspecialchars($_POST['titre'] ?? '');
            
            if(!isset($_SESSION['playlist'])){
                return '<p>Aucune playlist en session. <a href="?action=add-playlist">Créer une playlist</a></p>';
            }
            
            $playlist = $_SESSION['playlist'];
            
            if ($playlist->id) {
                Authz::checkPlaylistOwner($playlist->id);
            }
            
            if(substr($_FILES['audiofile']['name'],-4) !== '.mp3'){
                return $this->Formulaire('Fichier non valide', $titre);
            }
            
            $nomFichier = uniqid() . '.mp3';
            $destination = __DIR__ . '/../../../audio/' . $nomFichier;
            
            if (!is_dir(dirname($destination))) {
                mkdir(dirname($destination), 0777, true);
            }
            
            move_uploaded_file($_FILES['audiofile']['tmp_name'], $destination);
            
            $track = new PodcastTrack($titre, 'audio/' . $nomFichier);
            
            if ($playlist->id) {
                $repo = DeefyRepository::getInstance();
                $trackId = $repo->saveTrack($track);
                $track->id = $trackId;
                $position = $playlist->nbPistes + 1;
                $repo->addTrackToPlaylist($trackId, $playlist->id, $position);
            }
            
            $playlist->ajouterPiste($track);
            $_SESSION['playlist'] = $playlist;
            
            return '<p>Piste ajoutée avec succès.</p><a href="?action=add-track">Ajouter encore une piste</a>';
        } catch (AuthnException | AuthzException $e) {
            return "<p>Erreur d'autorisation: {$e->getMessage()}</p>";
        } catch (\Exception $e) {
            return $this->Formulaire("Erreur: {$e->getMessage()}", htmlspecialchars($_POST['titre'] ?? ''));
        }
    }
}
