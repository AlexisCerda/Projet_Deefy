<?php
namespace iutnc\deefy\action;
class Dispatcher{
    public function run() {
        $action = $_GET['action'] ?? 'default';

        switch ($action) {
            case 'add-user':
                $actionObj = new \iutnc\deefy\action\AddUserAction();
                break;
            case 'signin':
                $actionObj = new \iutnc\deefy\action\SigninAction();
                break;
            case 'add-playlist':
                $actionObj = new \iutnc\deefy\action\AddPlaylistAction();
                break;
            case 'add-track':
                $actionObj = new \iutnc\deefy\action\AddPodcastTrackAction();
                break;
            case 'display-playlist':
                $actionObj = new \iutnc\deefy\action\DisplayPlaylistAction();
                break;
            case 'mes-playlists':
                $actionObj = new \iutnc\deefy\action\DisplayUserPlaylistsAction();
                break;
            case 'current-playlist':
                $actionObj = new \iutnc\deefy\action\DisplayCurrentPlaylistAction();
                break;
            default:
                $actionObj = new \iutnc\deefy\action\DefaultAction();
                break;
        }
        $this->renderPage($actionObj->execute());
    }
    private function renderPage(string $html): void{
        $page = file_get_contents(__DIR__ . '/../../../Page.html');
        
        $page = str_replace('{{content}}', $html, $page);

        echo $page;
    }
}