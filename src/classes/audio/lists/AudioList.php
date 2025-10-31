<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;

class AudioList {
    public ?int $id = null;
    protected string $nom;
    protected int $nbPistes = 0;
    protected int $dureeTotale = 0;
    protected array $pistes = [];

    public function __construct(string $nom, array $pistes = []) {
        $this->nom = $nom;
        $this->pistes = $pistes;
        $this->nbPistes = count($pistes);
        $this->dureeTotale = array_reduce($pistes, function($c, $p) {
            return $c + ($p->duree ?? 0);
        }, 0);
    }

    public function __get(string $prop) {
        if (!property_exists($this, $prop)) {
            throw new \Exception("invalid property : $prop");
        }
        return $this->$prop;
    }
}
