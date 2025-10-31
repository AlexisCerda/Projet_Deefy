<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class AddUserAction {

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
<form method="POST" action="?action=add-user">
    <label>Email: <input type="email" name="email" value="$emailValue" required></label><br>
    <label>Mot de passe: <input type="password" name="passwd" required></label><br>
    <label>Confirmer mot de passe: <input type="password" name="passwd2" required></label><br>
    <button type="submit">S'inscrire</button>
</form>
$errorMessage
HTML;
    }

    private function handlePost(): string {
        $email = htmlspecialchars($_POST['email'] ?? '');
        
        if ($_POST['passwd'] !== $_POST['passwd2']) {
            return $this->getForm("Erreur: Les mots de passe ne correspondent pas", $email);
        }

        try {
            AuthnProvider::register($email, $_POST['passwd']);
            return "<p>Inscription réussie. Vous pouvez maintenant vous connecter.</p>";
        } catch (AuthnException $e) {
            return $this->getForm("Erreur: {$e->getMessage()}", $email);
        }
    }
}
