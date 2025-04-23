<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('app.welcome_to') }} CamboBrew</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
<!-- In the head section or just before closing </body> tag -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <!-- Khmer font support -->
    <link href="https://fonts.googleapis.com/css2?family=Hanuman:wght@400;700&display=swap" rel="stylesheet">
    
    @stack('styles')
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header text-center py-4">
                <img src="{{ asset('images/logo.jpg') }}" alt="CamboBrew Logo" class="img-fluid sidebar-logo">
            </div>
            
            <ul class="nav flex-column sidebar-menu">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> 
                        <span>{{ __('app.dashboard') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                        <i class="fas fa-coffee"></i> 
                        <span>{{ __('app.products') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                        <i class="fas fa-list"></i> 
                        <span>{{ __('app.categories') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                        <i class="fas fa-chart-line"></i> 
                        <span>{{ __('app.sales') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <i class="fas fa-users"></i> 
                        <span>{{ __('app.users') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                        <i class="fas fa-shopping-cart"></i> 
                        <span>{{ __('app.orders') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}" href="{{ route('pos.index') }}">
                        <i class="fas fa-cash-register"></i> 
                        <span>{{ __('app.pos') }}</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content Wrapper -->
        <div class="content-wrapper" id="content-wrapper">
            <!-- Header Navbar -->
            <header class="main-header">
                <div class="header-left">
                    <button type="button" class="sidebar-toggle" id="toggle-sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4 class="header-title">@yield('title', __('app.dashboard'))</h4>
                </div>
                
                <div class="header-right">
                    <!-- Language Selector -->
                    <div class="dropdown language-dropdown">
                        <button class="btn dropdown-toggle" type="button" id="languageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-globe"></i>
                            <span>{{ config('app.available_locales')[app()->getLocale()] ?? 'English' }}</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="languageDropdown">
                            @foreach(config('app.available_locales', ['en' => 'English']) as $locale => $language)
                                <a class="dropdown-item {{ app()->getLocale() == $locale ? 'active' : '' }}" 
                                   href="{{ route('language.switch', $locale) }}">
                                    {{ $language }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Time display -->
                    <div class="current-time">
                        <i class="far fa-clock"></i>
                        <span id="current-time-display">{{ now()->locale(app()->getLocale())->format('h:i A') }}</span>
                    </div>
                    
                    <!-- User dropdown -->
                    <div class="dropdown user-dropdown">
                        <button class="btn dropdown-toggle" type="button" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user-circle"></i>
                            <span>{{ Auth::user()->name ?? 'User' }}</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user-cog"></i> {{ __('app.profile') }}
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> {{ __('app.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="main-container">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="main-footer">
                <div class="footer-content">
                    <p>&copy; {{ date('Y') }} CamboBrew Coffee Shop. {{ __('app.all_rights_reserved') }}.</p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Update current time
        function updateCurrentTime() {
            const now = new Date();
            const options = { 
                hour: 'numeric', 
                minute: '2-digit', 
                hour12: true 
            };
            document.getElementById('current-time-display').textContent = now.toLocaleTimeString('{{ app()->getLocale() }}', options);
        }
        
        // DOM Ready
        $(document).ready(function() {
            // Sidebar toggle
            $('#toggle-sidebar').on('click', function() {
                $('.app-wrapper').toggleClass('sidebar-collapsed');
            });
            
            // Initialize time and update every minute
            updateCurrentTime();
            setInterval(updateCurrentTime, 60000);
            
            // Set html lang attribute
            document.documentElement.lang = "{{ app()->getLocale() }}";
        });
    </script>
    
    @stack('scripts')
</body>
</html>