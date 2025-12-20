<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Admin</div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Inventory
    </div>

    @php
        $inventoryActive =
            request()->is('categories*') ||
            request()->is('materials*') ||
            request()->is('manage-materials*') ||
            request()->is('material-transactions*');
    @endphp

    <!-- Inventory Menu -->
    <li class="nav-item {{ $inventoryActive ? 'active' : '' }}">
        <a class="nav-link {{ $inventoryActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse"
            data-target="#inventoryMenu" aria-expanded="{{ $inventoryActive ? 'true' : 'false' }}"
            aria-controls="inventoryMenu">

            <i class="fas fa-warehouse"></i>
            <span>Inventory</span>
        </a>

        <div id="inventoryMenu" class="collapse {{ $inventoryActive ? 'show' : '' }}" data-parent="#accordionSidebar">

            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Inventory Management:</h6>

                <!-- Categories -->
                <a class="collapse-item {{ request()->is('categories*') ? 'active' : '' }}"
                    href="{{ url('/categories') }}">
                    Categories
                </a>

                <!-- Materials Master -->
                <a class="collapse-item {{ request()->is('materials*') ? 'active' : '' }}"
                    href="{{ url('/materials') }}">
                    Materials
                </a>

                <!-- NEW: Manage Materials -->
                <a class="collapse-item {{ request()->is('manage-materials*') ? 'active' : '' }}"
                    href="{{ url('/manage-materials') }}">
                    Manage Materials
                </a>

                <!-- Inward / Outward -->
                <a class="collapse-item {{ request()->is('material-transactions*') ? 'active' : '' }}"
                    href="{{ url('/material-transactions') }}">
                    Manage Inward-Outward
                </a>
            </div>
        </div>
    </li>

</ul>
