<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to CamboBrew</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
        
        /* Logout button */
        .logout-btn {
            color: #dc3545;
            border: 1px solid #dc3545;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background-color: #dc3545;
            color: white;
        }
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
                    <a class="nav-link active" href="{{ route('dashboard') }}"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('products.index') }}"><i class="fa fa-cubes"></i> Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('categories.index') }}"><i class="fa fa-tags"></i> Categories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('sales.index') }}"><i class="fa fa-chart-line"></i> Sales</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users.index') }}"><i class="fa fa-users"></i> Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('orders.index') }}"><i class="fa fa-shopping-cart"></i> Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('pos.index') }}"><i class="fa fa-cash-register"></i> POS</a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content flex-grow-1" id="main-content">
            <!-- Header Navbar -->
            <header class="d-flex justify-content-between align-items-center bg-white shadow-sm p-3">
                <span class="sidebar-toggle" id="toggle-sidebar">&#9776;</span>
                <div class="d-flex align-items-center">
                <div class="dropdown mr-3">
    <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="languageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-globe mr-1"></i> {{ __('messages.language') }}
    </button>
    <div class="dropdown-menu" aria-labelledby="languageDropdown">
        <a class="dropdown-item" href="{{ route('lang.switch', 'en') }}">{{ __('messages.english') }}</a>
        <a class="dropdown-item" href="{{ route('lang.switch', 'km') }}">{{ __('messages.khmer') }}</a>
    </div>
</div>
                    <span id="current-time" class="mr-3">{{ now()->format('h:i A - l, F j, Y') }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm logout-btn">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </button>
                    </form>
                </div>
            </header>

            <main class="p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#toggle-sidebar').click(function() {
                $('#sidebar').toggleClass('closed');
                $('#main-content').toggleClass('expanded');
            });
            
            // Update current time every minute
            function updateTime() {
                var now = new Date();
                var hours = now.getHours();
                var minutes = now.getMinutes();
                var ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12;
                minutes = minutes < 10 ? '0' + minutes : minutes;
                
                var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                
                var timeString = hours + ':' + minutes + ' ' + ampm + ' - ' + days[now.getDay()] + ', ' + months[now.getMonth()] + ' ' + now.getDate() + ', ' + now.getFullYear();
                $('#current-time').text(timeString);
            }
            
            setInterval(updateTime, 60000); // Update every minute
        });
    </script>
</body>
</html>