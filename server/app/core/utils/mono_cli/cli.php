<?php

require_once __DIR__ . '/Generate.php';

if (isset($argv[1]) && (isset($argv[2]) || isset($argv[3]))) {

    $mode = $argv[1] ?? "";
    $type = $argv[2] ?? "";
    $filename = $argv[3] ?? "";

    if ($mode === "gen") {
        if ($type === 'con' or $type === 'controller') {
            Generate::createNewController($filename);
        } elseif ($type === 'mod' or $type === 'model') {
            Generate::createNewModel($filename);
        } elseif ($type === 'vw' or $type === 'view') {
            Generate::createView($filename);
        } elseif ($type === 'comp' or $type === 'component') {
            Generate::createNewComponent($filename);
        } elseif ($type === 'mid' or $type === 'middleware') {
            Generate::createMiddleware($filename);
        }
    } elseif ($mode === "fill") {
        Generate::fillSchema($argv[2]);
    }
}

echo "invalid mono command\n";
