<script>var enableSupportButton = '{{ config('dz.site_level.support_button') }}';</script>

@php
function loadScripts($scripts) {
    foreach ((array) $scripts as $script) {
        if (is_string($script)) {
            echo '<script src="' . asset($script) . '" type="text/javascript"></script>' . PHP_EOL;
        }
    }
}

loadScripts(config('dz.global.js.top'));
loadScripts(config('dz.pagelevel.' . $CurrentPage . '.js'));
loadScripts(config('dz.global.js.bottom'));
loadScripts(config('dz.pagelevel.' . $CurrentPage . '.js.bottom'));
@endphp
<script src="https://unpkg.com/feather-icons"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-steps/1.1.0/jquery.steps.min.js"></script>
<script>
  feather.replace();
  $(document).ready(function() {
    // $(".js-example-basic-single").select2();
  });
 
</script>