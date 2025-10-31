<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\exception\AuthnException;

class DisplayUserPlaylistsAction {
    
    public function execute(): string {
        try {
            $user = AuthnProvider::getSignedInUser();
            $repo = DeefyRepository::getInstance();
            $playlists = $repo->findPlaylistsByUserId($user['id']);
            
            if (empty($playlists)) {
                return "<p>Vous n'avez aucune playlist. <a href='?action=add-playlist'>Créer une playlist</a></p>";
            }
            
            $html = "<h2>Mes playlists</h2><ul>";
            foreach ($playlists as $pl) {
                $html .= "<li><a href='?action=display-playlist&id={$pl->id}'>" . htmlspecialchars($pl->nom) . "</a></li>";
            }
            $html .= "</ul>";
            
            return $html;
        } catch (AuthnException $e) {
            return "<p>Erreur d'authentification : " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}
