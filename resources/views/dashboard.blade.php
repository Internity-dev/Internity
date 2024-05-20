@extends('layouts.dashboard')

@section('dashboard-content')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">

        <!-- Content Start -->
        <div class="container-fluidd-flex flex-column justify-content-between py-4" style="height: 100vh">
            <div>
                <!-- Navbar Start -->
                <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
                    navbar-scroll="true">
                    <div class="container-fluid px-0">
                        <nav aria-label="breadcrumb">
                            <h6 class="font-weight-bolder mb-0 text-uppercase">
                                Dashboard
                            </h6>
                        </nav>
                        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                            {{-- <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                                <div class="input-group">
                                    <span class="input-group-text text-body">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="Type here..." />
                                </div>
                            </div> --}}
                            <ul class="ms-md-auto d-flex align-items-center navbar-nav justify-content-end">
                                <li class="justify-content-end dropdown pe-2 d-flex align-items-center">
                                    <a href="javascript:;" class="nav-link text-body font-weight-bold px-0"
                                        id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        <img src="{{ $user->avatar_url }}" class="avatar avatar-sm me-sm-1" alt="user"
                                            style="width: 30px; height: 30px;">
                                        <span class="d-sm-inline d-none">{{ $user->name }}</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4"
                                        aria-labelledby="dropdownMenuButton">
                                        <li class="mb-2">
                                            <a class="dropdown-item border-radius-md" href="{{ route('users.editProfile') }}">
                                                <div class="d-flex py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="text-sm font-weight-normal mb-1">
                                                            Edit Profil
                                                        </h6>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item border-radius-md" href="{{ route('change-password') }}">
                                                <div class="d-flex py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="text-sm font-weight-normal mb-1">
                                                            Ganti Password
                                                        </h6>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                                    <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                                        <div class="sidenav-toggler-inner">
                                            <i class="sidenav-toggler-line"></i>
                                            <i class="sidenav-toggler-line"></i>
                                            <i class="sidenav-toggler-line"></i>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
                <!-- Navbar End -->

                {{-- Dashboard Data Start --}}

                <div class="row">
                    {{-- Siswa Start --}}
                    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="numbers">
                                            <h6 class="text-sm mb-0 text-capitalize font-weight-bold">
                                                Siswa
                                            </h6>
                                            <h5 class="font-weight-bolder mb-0">
                                                {{ $students }}
                                                <!-- <span class="text-success text-sm font-weight-bolder">+55%</span> -->
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                            <i><iconify-icon icon="mdi:account-multiple"></iconify-icon></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Siswa End --}}

                    {{-- Guru Start --}}
                    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="numbers">
                                            <h6 class="text-sm mb-0 text-capitalize font-weight-bold">
                                                IDUKA
                                            </h6>
                                            <h5 class="font-weight-bolder mb-0">
                                                {{ $companies }}
                                                <!-- <span class="text-success text-sm font-weight-bolder">+3%</span> -->
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                            <i><iconify-icon icon="mdi:building"></iconify-icon></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Guru End --}}

                    {{-- Lowongan PKL Start --}}
                    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="numbers">
                                            <h6 class="text-sm mb-0 text-capitalize font-weight-bold">
                                                Lowongan PKL
                                            </h6>
                                            <h5 class="font-weight-bolder mb-0">
                                                {{ $vacancies }}
                                                <!-- <span class="text-danger text-sm font-weight-bolder">-2%</span> -->
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                            <i class="bi bi-person-workspace"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Lowongan PKL End --}}

                    {{-- Status PKL Start --}}
                    <div class="col-lg-6 pt-4">
                        <div class="card z-index-2">
                            <div class="card-header pb-0">
                                <h6>Status PKL</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="chart">
                                <canvas
                                    id="myChart"
                                    height="300"
                                ></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Status PKL End --}}

                    {{-- Relevansi Start --}}
                    <div class="col-lg-6 pt-4">
                            <div class="card z-index-2">
                            <div class="card-header pb-0">
                                <h6>Relevansi</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="chart">
                                <canvas
                                    id="chart-line"
                                    height="300"
                                ></canvas>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    {{-- Relevansi End --}}

                    {{-- Waktu PKL Start --}}
                    {{-- Fullsize --}}
                    <div class="col-lg-12 pt-4">
                            <div class="card z-index-2">
                            <div class="card-header pb-0">
                                <h6>Waktu PKL</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="chart">
                                <canvas
                                    id="newChart"
                                    height="300"
                                ></canvas>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    {{-- Waktu PKL End --}}
                {{-- Dashboard Data End --}}
            </div>

            {{-- <div class="chart">
                <canvas id="myChart"></canvas>
            </div> --}}
        </div>
        <!-- Content End -->
    </main>
@endsection


@once
    @push('scripts')
        <script type="module">
            let studentStatus = {!! json_encode($studentStatus, JSON_HEX_TAG) !!};
            let monitors = {!! json_encode($monitors, JSON_HEX_TAG) !!};
            let internDurations = {!! json_encode($internDurations, JSON_HEX_TAG) !!};

            console.log(internDurations);

            const ctx = document.getElementById('myChart');
            const ctx2 = document.getElementById('chart-line');
            const ctx3 = document.getElementById('newChart');

            const gradient1 = ctx.getContext('2d').createLinearGradient(50, 0, 0, 150);
            gradient1.addColorStop(0, '#ff667c');
            gradient1.addColorStop(1, '#ea0606');

            const gradient2 = ctx.getContext('2d').createLinearGradient(130, 0, 0, 150);
            gradient2.addColorStop(0, '#21d4fd');
            gradient2.addColorStop(1, '#2152ff');

            const gradient3 = ctx.getContext('2d').createLinearGradient(90, 0, 0, 150);
            gradient3.addColorStop(0, '#98ec2d');
            gradient3.addColorStop(1, '#17ad37');

            const gradient4 = ctx2.getContext('2d').createLinearGradient(90, 0, 0, 150);
            gradient4.addColorStop(0, '#627594');
            gradient4.addColorStop(1, '#a8b8d8');

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Belum PKL', 'Sedang PKL', 'Selesai PKL'],
                    datasets: [{
                        label: 'Jumlah',
                        data: [studentStatus['not_intern'], studentStatus['intern'], studentStatus['finished']],
                        backgroundColor: [
                            gradient1,
                            gradient2,
                            gradient3
                            // "#FF6B6B", // warna untuk data 1
                            // "#4D96FF", // warna untuk data 2
                            // "#6BCB77"  // warna untuk data 3
                        ],
                        hoverOffset: 4
                        // borderColor: [
                        //     "#FF6B6B",   // warna border untuk data 1
                        //     "#4D96FF",   // warna border untuk data 2
                        //     "#6BCB77"    // warna border untuk data 3
                        // ],
                        // borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    // scales: {
                    //     y: {
                    //     beginAtZero: true
                    //     }
                    // }
                }
            });

            new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: ['Sangat Relevan', 'Relevan', 'Kurang Relevan', 'Tidak Relevan'],
                    datasets: [{
                        label: 'Jumlah',
                        data: [monitors['4'], monitors['3'], monitors['2'], monitors['1']],
                        backgroundColor: [
                            gradient1,
                            gradient2,
                            gradient3,
                            gradient4
                            // "#FF6B6B", // warna untuk data 1
                            // "#4D96FF", // warna untuk data 2
                            // "#6BCB77"  // warna untuk data 3
                        ],
                        hoverOffset: 4
                        // borderColor: [
                        //     "#FF6B6B",   // warna border untuk data 1
                        //     "#4D96FF",   // warna border untuk data 2
                        //     "#6BCB77"    // warna border untuk data 3
                        // ],
                        // borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    // scales: {
                    //     y: {
                    //     beginAtZero: true
                    //     }
                    // }
                }
            });

            new Chart(ctx3, {
                type: 'bar',
                data: {
                    datasets: [{
                        data: internDurations,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            display: false
                        },
                    }
                    // scales: {
                    //     y: {
                    //     beginAtZero: true
                    //     }
                    // }
                }
            });
        </script>
    @endpush
@endonce
