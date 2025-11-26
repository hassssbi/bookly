{{-- resources/views/layouts/partials/navbar.blade.php --}}
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    {{-- Left navbar links --}}
    <ul class="navbar-nav">
        <li class="nav-item">
            {{-- Toggle sidebar --}}
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('dashboard') }}"
                class="nav-link {{ request()->is('/') || request()->is('dashboard') ? 'active' : '' }}">Home</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('shop.index') }}" class="nav-link {{ request()->is('shop*') ? 'active' : '' }}">Shop</a>
        </li>
    </ul>

    {{-- Right navbar links --}}
    <ul class="navbar-nav ml-auto">
        {{-- Simple user dropdown --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-user"></i>
                <span class="ml-1">
                    {{ auth()->user()->name ?? 'Guest' }}
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <span class="dropdown-item-text">
                    Role: <strong>{{ ucfirst(auth()->user()->role) ?? '-' }}</strong>
                </span>
                <div class="dropdown-divider"></div>
                {{-- If you later add web logout route, use that. For now just link to frontend logout --}}
                <a href="{{ route('logout') }}" class="dropdown-item"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>
