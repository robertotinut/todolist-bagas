<!DOCTYPE html>
<html lang="en">

<meta charset="utf-8" />
<title>@yield('title', ' | FabKin Admin & Dashboards Template')</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta content="Admin & Dashboards Template" name="description" />
<meta content="Pixeleyez" name="author" />

<!-- layout setup -->
<script type="module" src="{{ asset('assets/' . 'js/layout-setup.js') }}"></script>

<!-- App favicon -->
<link rel="shortcut icon" href="{{ asset('assets/' . 'images/k_favicon_32x.png') }}">

@yield('css')
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@include('partials.head-css')
@livewireStyles

<body>

    @include('partials.header')
    @include('partials.sidebar')
    @include('partials.horizontal')

    <main class="app-wrapper">
        <div class="container-fluid">

            @include('partials.page-title')

            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif

        </div> <!-- end container-fluid -->
    </main> <!-- end app-wrapper -->

    @include('partials.switcher')
    @include('partials.scroll-to-top')

    @include('partials.vendor-scripts')
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    @livewireScripts
    @yield('js')

</body>

</html>
