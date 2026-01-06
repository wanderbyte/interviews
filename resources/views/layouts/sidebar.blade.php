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

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#masterMenu"
            aria-expanded="false" aria-controls="masterMenu">
            <i class="fas fa-warehouse"></i>
            <span>Masters</span>
        </a>

        <div id="masterMenu" class="collapse {{ request()->routeIs('masters.*') ? 'show' : '' }}"
            aria-labelledby="headingMasters" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Master Management</h6>

                <!-- Standards -->
                <a class="collapse-item {{ request()->routeIs('masters.standards*') ? 'active' : '' }}"
                    href="{{ route('masters.standards') }}">
                    <i class="fas fa-layer-group mr-2"></i> Standards
                </a>

                <!-- States -->
                <a class="collapse-item {{ request()->routeIs('masters.states*') ? 'active' : '' }}"
                    href="{{ route('masters.states') }}">
                    <i class="fas fa-map-marked-alt mr-2"></i> States
                </a>

                <!-- Districts -->
                <a class="collapse-item {{ request()->routeIs('masters.districts*') ? 'active' : '' }}"
                    href="{{ route('masters.districts.index') }}">
                    <i class="fas fa-map mr-2"></i> Districts
                </a>

                <!-- Cities / Villages -->
                <a class="collapse-item {{ request()->routeIs('masters.cities*') ? 'active' : '' }}"
                    href="{{ route('masters.cities.index') }}">
                    <i class="fas fa-city mr-2"></i> Cities / Villages
                </a>

            </div>
        </div>
    </li>

</ul>
