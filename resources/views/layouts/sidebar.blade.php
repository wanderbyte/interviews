<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Admin</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Inventory
    </div>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#inventoryMenu"
            aria-expanded="true" aria-controls="inventoryMenu">
            <i class="fas fa-warehouse"></i>
            <span>Inventory</span>
        </a>

        <div id="inventoryMenu" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Inventory Management:</h6>

                <a class="collapse-item" href="{{ url('/categories') }}">
                    Category
                </a>

                <a class="collapse-item" href="{{ url('/materials') }}">
                    Material
                </a>

                <a class="collapse-item" href="{{ url('/material-transactions/create') }}">
                    Inward / Outward
                </a>

                <a class="collapse-item" href="{{ url('/materials-manage') }}">
                    Manage Materials
                </a>
            </div>
        </div>
    </li>

</ul>
