<?php
error_reporting(E_ERROR);

$output = [];
$dossiers = ['prod', 'dev'];

foreach ($dossiers as $dos) {
    $dossier = realpath("../var/cache/{$dos}");

    if ($dossier) {
        $dir_iterator = new RecursiveDirectoryIterator($dossier);
        $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::CHILD_FIRST);

        // On supprime chaque dossier et chaque fichier du dossier cible
        foreach ($iterator as $fichier) {
            $fichier->isDir() ? rmdir($fichier) : unlink($fichier);
        }

        // On supprime le dossier cible
        rmdir($dossier);
        $output[] = "Dossier <b>{$dos}</b> supprimer avec succès.";
    }
}

print_r($output);
