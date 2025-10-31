<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists\AudioList;

class AudioListRenderer extends Renderer {
    protected AudioList $list;

    public function __construct(AudioList $list) {
        $this->list = $list;
    }

    public function render(int $selector = self::COMPACT): string {
        $html = "<h2>" . htmlspecialchars($this->list->nom) . "</h2><ul>";
        foreach ($this->list->pistes as $p) {
            $data = json_decode((string)$p, true);
            $html .= "<li>" . htmlspecialchars($data['titre']) . " - " . htmlspecialchars($data['nom']) . "</li>";
        }
        $html .= "</ul><p>{$this->list->nbPistes} pistes</p>";
        return $html;
    }
}
