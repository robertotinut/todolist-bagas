<!DOCTYPE html>
<html lang="en">

<meta charset="utf-8" />
<title>@yield('title', 'SLADA - Aplikasi Manajemen Tugas & Kanban Board')</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta content="SLADA adalah aplikasi manajemen tugas, proyek, dan kolaborasi tim dengan papan Kanban interaktif untuk meningkatkan produktivitas." name="description" />
<meta content="SLADA Team" name="author" />

<!-- layout setup -->
<script type="module" src="{{ asset('assets/' . 'js/layout-setup.js') }}"></script>

<!-- App favicon -->
<link rel="shortcut icon" href="{{ asset('assets/' . 'images/k_favicon_32x.png') }}">

@yield('css')
@include('partials.head-css') 

<body>

@yield('content')

@include('partials.vendor-scripts')  

@yield('js')

</body>

</html>