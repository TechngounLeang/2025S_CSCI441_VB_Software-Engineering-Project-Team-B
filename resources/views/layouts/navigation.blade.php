<section class="sidebar">
    <div class="user-panel d-flex align-items-center">
        <div class="pull-left image">
            <img src="{{ asset('user-profile.png') }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
            <p>{{ \Auth::user()->name }}</p>
            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
    </div>
    
    <ul class="nav flex-column sidebar-menu">
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/home') }}"><i class="fa fa-dashboard"></i> Dashboard</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('products.index') }}"><i class="fa fa-cubes"></i> Product</a>
        </li>
        
    </ul>
</section>