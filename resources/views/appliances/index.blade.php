{{-- @php
    dd($appliances);
@endphp --}}

@extends('layouts.dashboard')

@section('dashboard-content')
    <x-table routeCreate="{{ route('appliances.create', ['vacancies' => encrypt($vacancy->id)]) }}"
        pageName="Pendaftar {{ $vacancy->name }}" permissionCreate="appliance-create" :pagination="$appliances" :tableData="$appliances">

        <x-slot:thead>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Kelola
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-25">
                Tanggal Daftar
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-25">
                Nama
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Kompetensi Keahlian
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-50">
                Keahlian
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
                CV
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
                Status
            </th>
        </x-slot:thead>

        <x-slot:tbody>
            @foreach ($appliances as $data)
                <tr>
                    <td class="text-center">
                        <form action="{{ route('appliances.accept', encrypt($data->id)) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button id="button-accept-{{ $data->id }}" class="btn button-appliances btn-success text-xs"
                                data-status='accept' data-bs-toggle="tooltip" data-bs-placement="bottom" title="Terima"
                                type="button"><i class="bi bi-check-lg"></i></button>
                        </form>
                        <form action="{{ route('appliances.reject', encrypt($data->id)) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button id="button-reject-{{ $data->id }}" class="btn button-appliances btn-danger text-xs"
                                data-status='reject' data-bs-toggle="tooltip" data-bs-placement="bottom" title="Tolak"
                                type="button"><i class="bi bi-x-lg"></i></button>
                        </form>
                    </td>
                    <td class="text-sm text-center">{{ $data->created_at->format('d-m-Y') }}</td>
                    <td class="text-sm text-center">{{ $data->user->name }}</td>
                    <td class="text-sm text-center">{{ $data->user->departments()->first()->name }}</td>
                    <td class="text-sm">
                        <ul>
                            @if ($data->skills)
                                @foreach (explode(',', $data->user->skills) as $skill)
                                    <li>{{ $skill }}</li>
                                @endforeach
                            @endif
                        </ul>
                    </td>
                    <td class="text-sm text-center">
                        @if ($data->user->resume)
                            <a href="{{ url('storage/' . $data->user->resume) }}" target="_blank"
                                class="btn btn-info text-xs" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                title="Lihat CV"><i class="bi bi-file-earmark-text"></i></a>
                        @else
                            <span class="badge badge-sm bg-gradient-danger">Tidak ada</span>
                        @endif
                    </td>
                    <td class="text-sm text-center">
                        @can('appliance-approve')
                            @if ($data->status == 'accepted')
                                <span class="badge badge-sm bg-gradient-success">Diterima</span>
                            @elseif ($data->status == 'rejected')
                                <span class="badge badge-sm bg-gradient-danger">Ditolak</span>
                            @elseif($data->status == 'pending')
                                <span class="badge badge-sm bg-gradient-warning">Menunggu</span>
                            @endif
                        @endcan
                    </td>
                </tr>
            @endforeach
        </x-slot:tbody>
    </x-table>
@endsection

@once
    @push('scripts')
        <script type="module">
            $('.button-appliances').on('click', function(){
                const buttonId = $(this).attr('id');
                const status = $(this).data('status');

                utils.useStatusButton({buttonId: buttonId, status: status})
            })
        </script>
    @endpush
@endonce
