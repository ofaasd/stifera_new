<script>var enableSupportButton = '{{ config('dz.site_level.support_button') }}';</script>

@php
if (!function_exists('loadScripts')) {
    function loadScripts($scripts) {
        foreach ((array) $scripts as $script) {
            if (is_string($script)) {
                $version = (strpos($script, 'custom.js') !== false) ? '?v=' . time() : '';
                echo '<script src="' . asset($script) . $version . '" type="text/javascript"></script>' . PHP_EOL;
            }
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
  
  if (typeof $.fn.dataTable !== 'undefined') {
      $.extend(true, $.fn.dataTable.defaults, {
          language: {
              paginate: {
                  previous: '<',
                  next: '>'
              }
          }
      });
  }

  $(document).ready(function() {
    // ── Mobile Sidebar User-Friendly Enhancements ──
    if ($('.sidebar-backdrop').length === 0) {
      $('body').append('<div class="sidebar-backdrop"></div>');
    }

    function syncSidebarMobile() {
      if ($(window).width() < 992) {
        if ($('#main-wrapper').hasClass('menu-toggle')) {
          $('.sidebar-backdrop').addClass('show');
          $('body').css('overflow', 'hidden');
        } else {
          $('.sidebar-backdrop').removeClass('show');
          $('body').css('overflow', '');
        }
      } else {
        $('.sidebar-backdrop').removeClass('show');
        $('body').css('overflow', '');
      }
    }

    // Pastikan sidebar tertutup saat awal muat halaman di HP / Tablet
    if ($(window).width() < 992) {
      $('#main-wrapper').removeClass('menu-toggle');
      $('.hamburger').removeClass('is-active');
    }

    // Sinkronisasi saat tombol hamburger / nav-control diklik
    $(document).on('click', '.nav-control', function() {
      setTimeout(syncSidebarMobile, 60);
    });

    // Tutup sidebar saat klik backdrop atau tombol close mobile
    $(document).on('click', '.sidebar-mobile-close-btn, .sidebar-backdrop', function() {
      $('#main-wrapper').removeClass('menu-toggle');
      $('.hamburger').removeClass('is-active');
      syncSidebarMobile();
      try {
        localStorage.setItem("sidebar_collapsed", "false");
      } catch(e) {}
    });

    // Tutup sidebar saat klik link tujuan (non-accordion) di HP / Tablet
    $(document).on('click', '.deznav .metismenu a:not(.has-arrow)', function() {
      if ($(window).width() < 992) {
        $('#main-wrapper').removeClass('menu-toggle');
        $('.hamburger').removeClass('is-active');
        syncSidebarMobile();
      }
    });

    $(window).on('resize', function() {
      syncSidebarMobile();
    });
  });
</script>