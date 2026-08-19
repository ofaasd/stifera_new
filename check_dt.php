<?php

function replaceFavicon($dir)
{
    $files = scandir($dir);
    foreach ($files as $f) {
        if ($f == '.' || $f == '..')
            continue;

        $path = $dir . DIRECTORY_SEPARATOR . $f;
        if (is_dir($path)) {
            replaceFavicon($path);
        } else if (substr($path, -10) == '.blade.php') {
            $c = file_get_contents($path);
            if (strpos($c, 'favicon') !== false) {
                // Typical NexaDash pattern
                $pattern = '/<link.*?rel=["\']shortcut icon["\'].*?>/i';
                // replace with BOTH ico and png
                $replacement = '<link rel="icon" type="image/x-icon" href="{{ asset(\'images/favicon.ico\') }}">' . "\n" . '<link rel="shortcut icon" type="image/png" href="{{ asset(\'images/favicon.png\') }}">';
                $newC = preg_replace($pattern, $replacement, $c);
                if ($newC && $newC !== $c) {
                    file_put_contents($path, $newC);
                    echo "Modified: $path\n";
                }

                // If it used config, it might be matched by the regex since the entire tag is matched.
            }
        }
    }
}

replaceFavicon('resources/views');
