<?php

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\exception\AuthzException;

class DisplayPlaylistAction {
    
    public function execute(): string {
        $id = $_GET['id'] ?? null;
        
        if (!$id || !is_numeric($id)) {
            return "<p>Identifiant de playlist manquant ou invalide</p>";
        }
        
        try {
            Authz::checkPlaylistOwner((int)$id);
        } catch (AuthnException $e) {
            return "<p>Erreur d'authentification : " . htmlspecialchars($e->getMessage()) . "</p>";
        } catch (AuthzException $e) {
            return "<p>Erreur d'autorisation : " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        
        $repo = DeefyRepository::getInstance();
        $playlist = $repo->findPlaylistById((int)$id);
        
        if (!$playlist) {
            return "<p>Playlist introuvable</p>";
        }
        
        $_SESSION['playlist'] = $playlist;
        
        $renderer = new AudioListRenderer($playlist);
        $html = $renderer->render();
        $html .= '<br><a href="?action=add-track">Ajouter une piste</a>';
        
        return $html;
    }
}
