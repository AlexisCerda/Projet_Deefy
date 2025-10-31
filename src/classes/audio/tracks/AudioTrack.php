<?php

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;

class AudioTrack {
    public ?int $id = null;
    protected string $titre;
    protected string $artiste = "";
    protected string $genre = "";
    protected int $duree = 0;
    protected string $nom;

    public function __construct(string $titre, string $nom) {
        $this->titre = $titre;
        $this->nom = $nom;
    }

    public function __get(string $prop) {
        if (!property_exists($this, $prop)) {
            throw new InvalidPropertyNameException("invalid property : $prop");
        }
        return $this->$prop;
    }

    public function __set(string $prop, $val): void {
        if (!property_exists($this, $prop)) {
            throw new InvalidPropertyNameException("invalid property : $prop");
        }
        if (in_array($prop, ["titre","nom"], true)) {
            throw new InvalidPropertyValueException("Propriété non modifiable : $prop");
        }
        if ($prop === "duree") {
            $ival = (int)$val;
            if ($ival < 0) {
                throw new InvalidPropertyValueException("durée négative interdite");
            }
            $this->$prop = $ival;
            return;
        }
        $this->$prop = $val;
    }

    public function __toString(): string {
        $arr = [
            'id' => $this->id,
            'titre' => $this->titre,
            'artiste' => $this->artiste,
            'genre' => $this->genre,
            'duree' => $this->duree,
            'nom' => $this->nom,
        ];
        return json_encode($arr);
    }
}
