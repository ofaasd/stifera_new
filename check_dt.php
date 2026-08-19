<?php
$lines = file('routes/web.php');
$out = [];
foreach ($lines as $i => $l) {
    if (stripos($l, 'yudisium') !== false) {
        $out[] = trim($l);
    }
}
file_put_contents('out.txt', implode("\n", $out));
