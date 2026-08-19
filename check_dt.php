<?php
$content = file_get_contents('app/Http/Controllers/mahasiswa/YudisiumMahasiswaController.php');

// Fix the class opening bracket issue
$content = str_replace(
    "class YudisiumMahasiswaController extends Controller
    protected function getMandatoryKeys(\$id_prodi)",
    "class YudisiumMahasiswaController extends Controller
{
    protected function getMandatoryKeys(\$id_prodi)",
    $content
);

// Remove the orphaned bracket at line 42
$content = str_replace(
    "    }

{
    // List mandatory items according to the user request.",
    "    }

    // List mandatory items according to the user request.",
    $content
);

file_put_contents('app/Http/Controllers/mahasiswa/YudisiumMahasiswaController.php', $content);
echo "Fixed spacing\n";
