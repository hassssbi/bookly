{{-- resources/views/layouts/partials/sidebar.blade.php --}}
<aside class="main-sidebar sidebar-light-cyan elevation-3">
    {{-- Brand Logo --}}
    <a href="{{ route('dashboard') }}" class="brand-link text-center">
        {{--  <img src="{{ asset('adminlte/dist/img/AdminLTELogo.png') }}" alt="Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8"> --}}
        <img src="{{ asset('adminlte/dist/img/bookly-logo-long.png') }}" alt="" height="50px">
    </a>

    {{-- Sidebar --}}
    <div class="sidebar">
        {{-- User panel --}}
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            {{-- <div class="image">
                <img src="{{ asset('adminlte/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                    alt="User Image">
            </div> --}}
            <div class="info">
                <a href="#" class="d-block">
                    {{ auth()->user()->name ?? 'Guest' }}
                </a>
                @auth
                    <small class="text-muted d-block">
                        {{ ucfirst(auth()->user()->role) }}
                    </small>
                @endauth
            </div>
        </div>

        {{-- Sidebar search (optional) --}}
        {{-- <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div> --}}

        {{-- Sidebar Menu --}}
        <nav class="mt-2">
            <ul class="nav nav-pills nav-flat nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                {{-- Dashboard (all roles) --}}
                @if (auth()->user()->role !== 'customer')
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                @endif

                <li class="nav-item">
                    <a href="{{ route('shop.index') }}"
                        class="nav-link {{ request()->is('shop*') || request()->is('customer/checkout*') || request()->is('customer/payment*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-store"></i>
                        <p>Shop</p>
                    </a>
                </li>

                @php
                    $user = auth()->user();
                @endphp

                {{-- Show this for customers (or any non-seller role) --}}
                @if ($user && $user->role !== 'seller' && $user->role !== 'admin')
                    <li class="nav-item">
                        <a href="{{ route('seller.apply.show') }}"
                            class="nav-link {{ request()->routeIs('seller.apply.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-store"></i>
                            <p>Apply as Seller</p>
                        </a>
                    </li>
                @endif

                {{-- ADMIN SECTION --}}
                @auth
                    @if (auth()->user()->role === 'admin')
                        <li class="nav-header">ADMIN</li>

                        <li class="nav-item">
                            <a href="{{ route('admin.categories.index') }}"
                                class="nav-link {{ request()->is('admin/categories*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>Manage Categories</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.sellers.index') }}"
                                class="nav-link {{ request()->is('admin/sellers*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-store"></i>
                                <p>Manage Sellers</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}"
                                class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user"></i>
                                <p>Manage Users</p>
                            </a>
                        </li>
                    @endif
                @endauth

                {{-- SELLER SECTION --}}
                @auth
                    @if (auth()->user()->role === 'seller')
                        <li class="nav-header">SELLER</li>

                        <li class="nav-item">
                            <a href="{{ route('seller.profile.show') }}"
                                class="nav-link {{ request()->is('seller/profile*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-id-card"></i>
                                <p>My Seller Profile</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('seller.books.index') }}"
                                class="nav-link {{ request()->is('seller/books*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>My Books</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('seller.orders.index') }}"
                                class="nav-link {{ request()->is('seller/orders*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-shopping-bag"></i>
                                <p>Orders from Customers</p>
                            </a>
                        </li>
                    @endif
                @endauth

                {{-- CUSTOMER / COMMON SECTION --}}
                @auth
                    @if (auth()->user()->role === 'customer')
                        <li class="nav-header">MY ACCOUNT</li>

                        <li class="nav-item">
                            <a href="{{ route('customer.orders.index') }}"
                                class="nav-link {{ request()->is('customer/orders*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-receipt"></i>
                                <p>My Orders</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('customer.wishlist.index') }}"
                                class="nav-link {{ request()->is('customer/wishlist*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-heart"></i>
                                <p>My Wishlist</p>
                            </a>
                        </li>
                    @endif
                @endauth

            </ul>
        </nav>
    </div>
</aside>
