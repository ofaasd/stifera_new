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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />