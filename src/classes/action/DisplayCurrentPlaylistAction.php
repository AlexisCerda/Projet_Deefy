<?php

namespace iutnc\deefy\action;

use iutnc\deefy\render\AudioListRenderer;

class DisplayCurrentPlaylistAction {
    
    public function execute(): string {
        if (!isset($_SESSION['playlist'])) {
            return "<p>Aucune playlist courante. <a href='?action=add-playlist'>Créer une playlist</a></p>";
        }
        
        $playlist = $_SESSION['playlist'];
        $renderer = new AudioListRenderer($playlist);
        $html = $renderer->render();
        
        if ($playlist->id) {
            $html .= '<br><a href="?action=add-track">Ajouter une piste</a>';
        }
        
        return $html;
    }
}
