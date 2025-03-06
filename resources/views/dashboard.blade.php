@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('dashboard-content')
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
                        height="300"></canvas>
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
                        height="300"></canvas>
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
                    height="300"></canvas>
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

@endsection


@once
@push('scripts')
<script type="module">
    let studentStatus = @json($studentStatus);
    let monitors = @json($monitors);
    let internDurations = @json($internDurations);

    // console.log(internDurations);

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