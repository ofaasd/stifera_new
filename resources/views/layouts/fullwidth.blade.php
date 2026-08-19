<!DOCTYPE html>
<html lang="en">

<head>
@if(isset($baseHref)) <base href="../"> @endif

<title><?php echo !empty(config('dz.pagelevel.'.$CurrentPage.'.title')) ? config('dz.pagelevel.'.$CurrentPage.'.title').' | ' : '' ; echo config('dz.site_level.site_title'); ?></title>

@include('elements.meta', ['CurrentPage' => $CurrentPage])
<link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
<link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.png') }}">

@include('elements.page-css', ['CurrentPage' => $CurrentPage])

</head>

<body class="vh-100">

	@yield('content')

@include('elements.page-js', ['CurrentPage' => $CurrentPage])

@yield('local-js')
</body>

</html>