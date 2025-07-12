<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
   <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Softcopy Invoice</a>

        <div class="d-flex align-items-center gap-3">
            <span class="text-white">
                Selamat datang, {{ Auth::user()->name ?? 'Guest' }}
            </span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-outline-light btn-sm rounded-pill">Logout</button>
            </form>
        </div>
    </div>
</nav>


    <main class="container mb-5">
        @yield('content')
    </main>

    <footer class="text-center text-muted mb-3">
        &copy; 2025 Laravel. All rights reserved.
    </footer>

    @yield('scripts')
</body>
</html>
