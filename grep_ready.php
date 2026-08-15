<?php $lines = file('public/js/custom.js'); foreach($lines as $i => $l) { if (stripos($l, 'ready') !== false || stripos($l, 'load') !== false) { echo ($i+1) . ': ' . $l; } }
