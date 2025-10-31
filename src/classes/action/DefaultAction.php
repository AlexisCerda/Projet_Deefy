<?php

namespace iutnc\deefy\action;

class DefaultAction {
    
    public function execute(): string {
        $html = "<h1>Bienvenue sur Deefy</h1>";
        
        if (isset($_SESSION['user'])) {
            $html .= "<p>Connecté en tant que: " . htmlspecialchars($_SESSION['user']['email']) . "</p>";
            $html .= "<ul>";
            $html .= "<li><a href='?action=mes-playlists'>Mes playlists</a></li>";
            $html .= "<li><a href='?action=add-playlist'>Créer une playlist</a></li>";
            if (isset($_SESSION['playlist'])) {
                $html .= "<li><a href='?action=current-playlist'>Afficher la playlist courante</a></li>";
            }
            $html .= "</ul>";
        } else {
            $html .= "<ul>";
            $html .= "<li><a href='?action=signin'>Se connecter</a></li>";
            $html .= "<li><a href='?action=add-user'>S'inscrire</a></li>";
            $html .= "</ul>";
        }
        
        return $html;
    }
}
