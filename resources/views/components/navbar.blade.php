<!-- Navbar Start -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
    navbar-scroll="true">
    <div class="container-fluid d-flex flex-column flex-lg-row align-items-center justify-content-between px-0">

        <!-- Navbar Content (Avatar dan Title) -->
        <div class="d-flex flex-column flex-lg-row align-items-center w-100 justify-content-between">

            <!-- Title (Mobile: di bawah, Desktop: tetap di atas) -->
            <nav aria-label="breadcrumb" class="order-2 order-lg-1 text-center text-lg-start w-100 mt-2 mt-lg-0">
                <h6 class="font-weight-bolder mb-3 text-uppercase">@yield('title')</h6>
            </nav>

            <!-- User Avatar (Di kanan) -->
            <div class="order-1 order-lg-2 w-100">
                <ul class="navbar-nav d-flex flex-row align-items-center justify-content-between w-100">
                    <!-- Sidebar Toggle (Mobile) -->
                    <li class="nav-item d-lg-none">
                        <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                            <div class="sidenav-toggler-inner">
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                            </div>
                        </a>
                    </li>

                    <!-- Avatar -->
                    <li class="nav-item dropdown ms-auto">
                        <a href="javascript:;" class="nav-link text-body font-weight-bold px-0 d-flex align-items-center"
                            id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ $authUser->avatar_url }}" class="avatar avatar-sm me-2 rounded-circle" alt="user"
                                style="width: 30px; height: 30px;">
                            <span class="d-none d-lg-inline">{{ $authUser->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4 w-100"
                            aria-labelledby="dropdownMenuButton">
                            <li class="mb-2">
                                <a class="dropdown-item border-radius-md" href="{{ route('users.editProfile') }}">
                                    <div class="d-flex py-1">
                                        <h6 class="text-sm font-weight-normal mb-1">Edit Profil</h6>
                                    </div>
                                </a>
                            </li>
                            <li class="mb-2">
                                <a class="dropdown-item border-radius-md" href="{{ route('change-password') }}">
                                    <div class="d-flex py-1">
                                        <h6 class="text-sm font-weight-normal mb-1">Ganti Password</h6>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

        </div>

    </div>
</nav>
<!-- Navbar End -->