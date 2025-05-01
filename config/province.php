<?php

$path = public_path('assets/js/province.min.json');

$provinceObj = [];

if (file_exists($path)) {
    $json = file_get_contents($path);
    $decoded = json_decode($json, true);

    if (is_array($decoded)) {
        $provinceObj = $decoded;
    }
}

return [
    'obj' => $provinceObj,
    'json' => json_encode($provinceObj),
];
