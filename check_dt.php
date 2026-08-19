<?php
$content = file_get_contents('app/Http/Controllers/admin/YudisiumController.php');
$content = str_replace(
    "'angkatan_allowed' => 'required|string',",
    "'angkatan_allowed' => 'required|array|min:1',\n            'angkatan_allowed.*' => 'required|string',",
    $content
);
file_put_contents('app/Http/Controllers/admin/YudisiumController.php', $content);
