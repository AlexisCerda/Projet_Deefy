<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;

class Playlist extends AudioList {

    public function ajouterPiste(AudioTrack $p): void {
        $this->pistes[] = $p;
        $this->nbPistes = count($this->pistes);
        $this->dureeTotale += $p->duree;
    }

    public function supprimerPiste(int $index): void {
        if (isset($this->pistes[$index])) {
            $this->dureeTotale -= $this->pistes[$index]->duree;
            unset($this->pistes[$index]);
            $this->pistes = array_values($this->pistes);
            $this->nbPistes = count($this->pistes);
        }
    }

    public function ajouterListe(array $pistes): void {
        foreach ($pistes as $p) {
            if (!in_array($p, $this->pistes, true)) {
                $this->ajouterPiste($p);
            }
        }
    }

    public function __toString(): string {
        return json_encode([
            'id' => $this->id,
            'nom' => $this->nom,
            'nbPistes' => $this->nbPistes,
            'dureeTotale' => $this->dureeTotale,
            'pistes' => array_map(fn($p) => json_decode((string)$p, true), $this->pistes)
        ]);
    }
}
