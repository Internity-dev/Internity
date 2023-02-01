<!-- Sidenav Start -->
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-white"
    id="sidenav-main">
    <!-- Sidenav Header Start -->
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="{{ route('dashboard') }}">
            <img src="{{ asset('/img/logo-internity.png') }}" class="navbar-brand-img h-100" alt="main_logo" />
            <span class="ms-1 font-weight-bold">INTERNITY</span>
        </a>
    </div>
    <!-- Sidenav Header End -->

    <hr class="horizontal dark mt-0" />

    <!-- Sidenav Main Start -->
    <div class="navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <!-- Nav-Dashboard Start-->
            @foreach ($menus as $item)
                <li class="nav-item">
                    <a class="nav-link active" href="/{{ $item->url }}">
                        <div
                            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-speedometer"></i>
                        </div>
                        <span class="nav-link-text ms-1">{{ $item->name }}</span>
                    </a>
                </li>
            @endforeach
            <!-- Nav-Dashboard End-->
        </ul>
    </div>
    <!-- Sidenav Main End -->
</aside>
<!-- Sidenav End -->