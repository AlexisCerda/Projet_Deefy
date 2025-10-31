<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class SigninAction {

    public function execute(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->getForm();
        } else {
            return $this->handlePost();
        }
    }

    private function getForm(string $error = '', string $email = ''): string {
        $emailValue = htmlspecialchars($email);
        $errorMessage = $error ? "<p>$error</p>" : '';
        
        return <<<HTML
<form method="POST" action="?action=signin">
    <label>Email: <input type="email" name="email" value="$emailValue" required></label><br>
    <label>Mot de passe: <input type="password" name="passwd" required></label><br>
    <button type="submit">Se connecter</button>
</form>
$errorMessage
HTML;
    }

    private function handlePost(): string {
        try {
            AuthnProvider::signin($_POST['email'], $_POST['passwd']);
            return "<p>Authentification réussie. Bienvenue {$_SESSION['user']['email']}</p>";
        } catch (AuthnException $e) {
            return $this->getForm("Erreur: {$e->getMessage()}", htmlspecialchars($_POST['email'] ?? ''));
        }
    }
}
