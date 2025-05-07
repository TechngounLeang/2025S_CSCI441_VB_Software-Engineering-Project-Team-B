<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Cresences Bakery' }}</title>
    <link rel="icon" href="{{ asset('img/cresences-logo.webp') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/bakery.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/chatbot.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/form-validation.css') }}" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- CSRF Token for Ajax Requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Temporary styles for menu slides until CSS is properly loaded */
        .menu-slide {
            display: none;
        }
        .menu-slide.active {
            display: block;
        }
        .video-background::after {
            content:'';
            position:absolute;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:#00000080;
        }
    </style>
</head>

<body id="head-contents">
    <div class="container-fluid p-0">
        <!-- Header and Navigation -->
        <header class="py-3" role="banner">
            <nav class="navbar navbar-expand-lg navbar-light" style="background-color: transparent;">
                <div class="container-fluid">
                    <!-- Logo aligned to the left -->
                    <a class="navbar-brand" href="/">
                        <img src="{{ asset('img/cresences-logo.webp') }}" alt="cresences" aria-label="Cresences Home" width="120">
                    </a>

                    <!-- Toggle button for mobile view -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Navigation links aligned to the right -->
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item"><a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">Home</a></li>
                            
                            <!-- User authentication links -->
                            @auth
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'manager' || Auth::user()->role === 'cashier')
                                    <li><a href="{{ route('dashboard') }}" class="dropdown-item">Dashboard</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    @endif
                                    <li><a href="{{ route('profile.edit') }}" class="dropdown-item">My Profile</a></li>
                                    <li><a href="{{ route('orders.index') }}" class="dropdown-item">My Orders</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                            
                            <!-- Order button for authenticated users based on role -->
                            <li class="nav-item ms-2">
                                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'manager' || Auth::user()->role === 'cashier')
                                    <a href="{{ route('orders.index') }}" class="btn btn-primary {{ request()->routeIs('orders.*') ? 'active' : '' }}" id="order">Manage Orders</a>
                                @else
                                    <a href="{{ route('orders.create') }}" class="btn btn-primary {{ request()->routeIs('orders.create') ? 'active' : '' }}" id="order">Order Now</a>
                                @endif
                            </li>
                            @else
                            <!-- Login/register links for guests (no order button) -->
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}">Login</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('register') }}" class="btn btn-outline-primary {{ request()->routeIs('register') ? 'active' : '' }}">Sign Up</a>
                            </li>
                            @endauth
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        
        <!-- Main Content -->
        <main id="main-contents" role="main">
            <section class="video-content position-relative">
                <!-- Video background section using Bootstrap's responsive aspect ratio utility -->
                <div class="video-background ratio ratio-16x9">
                    <iframe src="https://www.youtube.com/embed/7nEinTO_MoI?autoplay=1&mute=1&playsinline=1&loop=1&playlist=7nEinTO_MoI&controls=0&disablekb=1" allowfullscreen frameborder="0"></iframe>
                </div>
                <!-- Content overlay section -->
                <div class="content position-absolute top-50 start-50 translate-middle text-center text-white">
                    <div class="intro">
                        <h1 class="intro-text display-4 fw-bold">"Where Every Crumb Tells a Story"</h1>
                    </div>
                </div>
            </section>
        </main>

        <!-- Product Gallery Section -->
        <section class="product-gallery py-5 bg-light">
            <div class="container">
                <div class="menu-title text-center mb-4">
                    <h2>Fresh From Our Bakery</h2>
                    <p class="lead">Products currently available for order</p>
                </div>
                
                @if(isset($availableProducts) && $availableProducts->count() > 0)
                    <div class="product-gallery-container" id="productGallery">
                        <div class="product-row">
                            @foreach($availableProducts as $product)
                                <div class="product-item">
                                    @if($product->photo_path)
                                        <img src="{{ asset('storage/' . $product->photo_path) }}" class="product-image" alt="{{ $product->name }}">
                                    @else
                                        <div class="product-image-placeholder">
                                            <i class="fas fa-bread-slice fa-3x text-secondary"></i>
                                        </div>
                                    @endif
                                    <div class="product-info">
                                        <h5 class="product-title">{{ $product->name }}</h5>
                                        <p class="product-category">{{ $product->category->name ?? 'Uncategorized' }}</p>
                                        <p class="product-description">
                                            {{ !empty($product->description) ? 
                                                (strlen($product->description) > 80 ? 
                                                substr($product->description, 0, 80) . '...' : 
                                                $product->description) : 
                                                'Freshly baked with premium ingredients' }}
                                        </p>
                                        <div class="product-footer">
                                            <span class="product-price">${{ number_format($product->price, 2) }}</span>
                                            <span class="product-stock {{ $product->stock_quantity <= 5 ? 'low' : ($product->stock_quantity <= 10 ? 'limited' : 'in-stock') }}">
                                                {{ $product->stock_quantity <= 5 ? 'Low Stock' : ($product->stock_quantity <= 10 ? 'Limited' : 'In Stock') }}
                                            </span>
                                        </div>
                                        <a href="{{ route('orders.create') }}" class="btn btn-outline-primary w-100 product-action">Order Now</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="gallery-scroll-controls">
                        <button class="gallery-scroll-button" id="scrollLeft"><i class="fas fa-chevron-left"></i></button>
                        <button class="gallery-scroll-button" id="scrollRight"><i class="fas fa-chevron-right"></i></button>
                    </div>
                @else
                    <div class="col-12 text-center py-4">
                        <p class="lead">No products currently available. Please check back soon!</p>
                    </div>
                @endif
            </div>
        </section>
        
        <!-- Special Menu Section -->
        <section class="specialmenu-contents py-5">
            <div class="container">
                <div class="menu-title text-center mb-4">
                    <h2>Our Essential Today</h2>
                </div>
                <div id="five-select" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <div class="col">
                        <div class="selection text-center">
                            <img src="{{ asset('img/cheerycheescake.png') }}" alt="cheesecake" class="img-fluid" loading="lazy">
                            <p>Cheery Cheesecake</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="selection text-center">
                            <img src="{{ asset('img/baguettesw.png') }}" alt="baguette" class="img-fluid" loading="lazy">
                            <p>Baguette Sandwich</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="selection text-center">
                            <img src="{{ asset('img/pancake.png') }}" alt="pancake" class="img-fluid" loading="lazy">
                            <p>Berry Pancake</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="selection text-center">
                            <img src="{{ asset('img/tart.png') }}" alt="tart" class="img-fluid" loading="lazy">
                            <p>Nobu's Miso Vanilla Tart</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="selection text-center">
                            <img src="{{ asset('img/croissant.png') }}" alt="croissant" class="img-fluid" loading="lazy">
                            <p>Croissant</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
                   
        <!-- Context with text Section -->
        <section id="text-contents">
            <div class="info">
                <img src="{{ asset('img/macarons.png') }}" alt="Image Hover Overlay Slide From The Bottom" class="image" loading="lazy">
                <div class="bottom-slide">
                    <h1 class="text">"We take pride in using only the freshest ingredients to craft our delicious desserts"</h1>
                </div>
            </div>
            <div class="info">
                <img src="{{ asset('img/bread.png') }}" alt="Image Hover Overlay Slide From The Bottom" class="image" loading="lazy">
                <div class="bottom-slide">
                    <h1 class="text">"Explore a world of flavors with our artisanal bread selection, made with the finest ingredients."</h1>
                </div>
            </div>
        </section>

        <!-- Bakery Recommendation Chatbot -->
        <section id="bakery-chatbot" class="py-5 bg-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h3 class="mb-0">Bakery Recommendation Assistant</h3>
                                <small>Ask our AI assistant for personalized drink and pastry recommendations!</small>
                            </div>
                            <div class="card-body">
                                <div id="chat-container" class="mb-3">
                                    <div class="chat-message bot-message">
                                        <p class="mb-0">Hello! I'm your AI bakery assistant powered by GPT-4o. What kind of pastry or drink are you in the mood for today?</p>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <input type="text" id="chat-input" class="form-control" placeholder="Type your preferences here..." aria-label="Chat input">
                                    <button class="btn btn-primary" id="send-button" type="button">Send</button>
                                </div>
                                <div class="mt-2 text-muted small">
                                    <p class="mb-0">Examples: "I'm in the mood for something with chocolate" or "What pairs well with coffee?"</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="OurLocation">
            <div id="map"></div>
        </section>            

        <!-- Footer -->
        <footer id="contacts" role="contentinfo">
            <div>
                <address>
                    <br>5955 MELROSE AVENUE
                    <br>LOS ANGELES, CALIFORNIA 90038
                    <br><a id="tel" href="tel:+1234567890">(123) 456-7890</a><br>
                    <a id="mail" href="mailto:contact@cresencesbakery.com">contact@cresencesbakery.com</a>
                    <br>
                    <br>
                </address>
            </div>
            <div>
                <p id="open-hour">Open Hours:<br> Monday-Friday:<b> 8:00 AM - 5:00 PM
                <br></b>Weekend: <b> 8:00 AM - 4:00 PM</b>
                </p>
            </div>
            <div id="connect-us">
                <a class="logo" href="https://facebook.com/" target="_blank"><img src="{{ asset('img/instagram.svg') }}" alt="instagram"></a>
                <a class="logo" href="https://instagram.com/" target="_blank"><img src="{{ asset('img/facebook.svg') }}" alt="Facebook"></a>
                <a class="logo" href="https://twitter.com/" target="_blank"><img src="{{ asset('img/twitter.svg') }}" alt="Twitter"></a>
            </div>
            <p id="sml-copyright">Copyright {{ date('Y') }} by Cresences Bakery. All Rights Reserved.</p>
        </footer>
    </div>
    
    <script src="https://maps.googleapis.com/maps/api/js?key=&callback=initMap" async defer></script>
    <script src="{{ asset('js/bakery.js') }}"></script>
    <script src="{{ asset('js/chatbot.js') }}"></script>
</body>
</html>