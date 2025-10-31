<?php

namespace iutnc\deefy\audio\tracks;

class AlbumTrack extends AudioTrack {
    protected string $album = "";
    protected int $annee = 0;
    protected int $numero = 0;

    public function __construct(string $titre, string $nom, string $album = "", int $numero = 0) {
        parent::__construct($titre, $nom);
        $this->album = $album;
        $this->numero = $numero;
    }

    public function __toString(): string {
        $arr = json_decode(parent::__toString(), true);
        $arr['album'] = $this->album;
        $arr['annee'] = $this->annee;
        $arr['numero'] = $this->numero;
        return json_encode($arr);
    }
}
