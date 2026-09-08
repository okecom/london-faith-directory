<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'London Faith Directory')
    </title>

    <link
        rel="stylesheet"
        href="{{ asset('css/app.css') }}"
    >

</head>

<body>

    @include('partials.header')

    <main class="main-content">

        <div class="container">

            @yield('content')

        </div>

    </main>

    @include('partials.footer')

    @stack('scripts')

</body>

</html>