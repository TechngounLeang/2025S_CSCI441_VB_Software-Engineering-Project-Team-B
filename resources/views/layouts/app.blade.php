<!DOCTYPE html>
<<<<<<< HEAD
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to CamboBrew</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
=======
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.welcome_to') }} CamboBrew</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Add Khmer font support -->
    <link href="https://fonts.googleapis.com/css2?family=Hanuman:wght@400;700&display=swap" rel="stylesheet">
>>>>>>> 0da82be (Modify pages to support khmer language partially)
    <style>
        /* Sidebar styles */
        .sidebar {
            width: 250px;
            transition: width 0.3s;
        }
        .sidebar.closed {
            width: 0;
            overflow: hidden;
        }

        /* Main content styles */
        .main-content {
            transition: margin-left 0.3s;
            margin-left: 250px;
            width: calc(100% - 250px);
        }
        .main-content.expanded {
            margin-left: 0;
            width: 100%;
        }

        /* Navbar header */
        .navbar {
            transition: width 0.3s;
        }

        /* Sidebar toggle button */
        .sidebar-toggle {
            cursor: pointer;
            font-size: 1.5rem;
        }
<<<<<<< HEAD
=======
        
        /* Language selector */
        .language-selector {
            margin-right: 20px;
        }
        
        /* Khmer font styling */
        html[lang="km"] body {
            font-family: 'Hanuman', serif;
        }
>>>>>>> 0da82be (Modify pages to support khmer language partially)
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar bg-light vh-100 position-fixed" id="sidebar">
            <div class="text-center py-3">
                <img src="{{ asset('images/logo.jpg') }}" alt="CamboBrew Logo" class="img-fluid" style="max-width: 150px;">
            </div>
            <ul class="nav flex-column p-3">
                <li class="nav-item">
<<<<<<< HEAD
                    <a class="nav-link active" href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('products.index') }}"><i class="fa fa-cubes"></i> Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('categories.index') }}"><i class="fa fa-user"></i> Categories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('sales.index') }}"><i class="fa fa-user"></i> Sales</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users.index') }}"><i class="fa fa-user"></i> Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('orders.index') }}"><i class="fa fa-user"></i> Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('pos.index') }}"><i class="fa fa-user"></i> POS</a>
=======
                    <a class="nav-link active" href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> {{ __('app.dashboard') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('products.index') }}"><i class="fa fa-cubes"></i> {{ __('app.products') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('categories.index') }}"><i class="fa fa-user"></i> {{ __('app.categories') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('sales.index') }}"><i class="fa fa-user"></i> {{ __('app.sales') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users.index') }}"><i class="fa fa-user"></i> {{ __('app.users') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('orders.index') }}"><i class="fa fa-user"></i> {{ __('app.orders') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('pos.index') }}"><i class="fa fa-user"></i> {{ __('app.pos') }}</a>
>>>>>>> 0da82be (Modify pages to support khmer language partially)
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content flex-grow-1" id="main-content">
            <!-- Header Navbar -->
            <header class="d-flex justify-content-between align-items-center bg-white shadow-sm p-3">
                <span class="sidebar-toggle" id="toggle-sidebar">&#9776;</span>
<<<<<<< HEAD
                <div class="ml-auto">
                    <span id="current-time">{{ now()->format('h:i A - l, F j, Y') }}</span>
=======
                <div class="d-flex align-items-center">
                    <!-- Language Selector -->
                    <div class="language-selector">
                        <select class="form-control" id="language-select" onchange="changeLanguage(this)">
                            @foreach(config('app.available_locales', ['en' => 'English']) as $locale => $language)
                                <option value="{{ $locale }}" {{ app()->getLocale() == $locale ? 'selected' : '' }}>
                                    {{ $language }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <span id="current-time">{{ now()->locale(app()->getLocale())->format('h:i A - l, F j, Y') }}</span>
>>>>>>> 0da82be (Modify pages to support khmer language partially)
                </div>
            </header>

            <main class="p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#toggle-sidebar').click(function() {
                $('#sidebar').toggleClass('closed');
                $('#main-content').toggleClass('expanded');
            });
<<<<<<< HEAD
        });
    </script>
     @stack('scripts')
</body>
</html>
=======
            
            // Set html lang attribute dynamically
            document.documentElement.lang = "{{ app()->getLocale() }}";
        });
        
        function changeLanguage(select) {
            window.location.href = '/language/' + select.value;
        }
    </script>
    @stack('scripts')
</body>
</html>
>>>>>>> 0da82be (Modify pages to support khmer language partially)
