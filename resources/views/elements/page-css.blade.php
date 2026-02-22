@php
function loadStyles($styles) {
    foreach ((array) $styles as $style) {
        if (is_string($style)) {
            echo '<link href="' . asset($style) . '" rel="stylesheet" type="text/css"/>' . PHP_EOL;
        }
    }
}

loadStyles(config('dz.global.css.top'));
loadStyles(config('dz.pagelevel.' . $CurrentPage . '.css'));
loadStyles(config('dz.global.css.bottom'));
@endphp
<link rel="stylesheet" href="{{ asset('css/feathericon.min.css') }}">