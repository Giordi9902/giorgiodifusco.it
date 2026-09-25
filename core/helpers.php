<?php

/**
 * URL di un asset statico con cache-busting basato sulla data di modifica del file,
 * così il browser riscarica il file solo quando cambia davvero.
 */
function asset(string $path): string
{
    $path = ltrim($path, '/');
    $file = __DIR__ . '/../public/' . $path;
    $version = is_file($file) ? filemtime($file) : null;

    return BASE_URL . '/public/' . $path . ($version ? '?v=' . $version : '');
}
