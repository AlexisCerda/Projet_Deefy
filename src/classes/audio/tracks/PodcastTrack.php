<?php

namespace iutnc\deefy\audio\tracks;

class PodcastTrack extends AudioTrack {
    protected string $date = "";
    protected string $auteur = "";

    public function __construct(string $titre, string $nom) {
        parent::__construct($titre, $nom);
    }

    public function __toString(): string {
        $arr = json_decode(parent::__toString(), true);
        $arr['date'] = $this->date;
        $arr['auteur'] = $this->auteur;
        return json_encode($arr);
    }
}
