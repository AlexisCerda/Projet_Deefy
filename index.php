<?php
declare(strict_types =1);
require_once 'vendor/autoload.php';
use iutnc\deefy\action\Dispatcher;
session_start();

\iutnc\deefy\repository\DeefyRepository::setConfig(__DIR__ . '/config/deefy.db.ini');

$d = new Dispatcher();
$d->run();



