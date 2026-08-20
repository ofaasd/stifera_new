<?php
$c = file_get_contents('app/Http/Controllers/PegawaiPertemuanController.php');
$c = str_replace(
    "->where('mjt.id_dosen', \$biodataId)",
    "->where(function(\$q) use (\$biodataId) {\n                \$q->where('mjt.id_dosen', \$biodataId)->orWhere('mjt.id_dosen2', \$biodataId);\n            })",
    $c
);
$c = str_replace(
    "->where('id_dosen', \$biodataId)",
    "->where(function(\$q) use (\$biodataId) {\n                \$q->where('id_dosen', \$biodataId)->orWhere('id_dosen2', \$biodataId);\n            })",
    $c
);
file_put_contents('app/Http/Controllers/PegawaiPertemuanController.php', $c);
echo "Added pengampu 2";
