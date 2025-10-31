<?php

namespace iutnc\deefy\render;

abstract class Renderer {
    public const COMPACT = 1;
    public const LONG = 2;

    public abstract function render(int $selector): string;
}
