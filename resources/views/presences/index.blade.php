@extends('layouts.dashboard')
@section('title', 'Riwayat Kehadiran ' . $userName)

@section('dashboard-content')
<x-table pageName="Riwayat Kehadiran {{ $userName }}" :pagination="$presences"
    :tableData="$presences">
    <x-slot:thead>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
            Kelola
        </th>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
            Tanggal
        </th>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
            Jam Masuk
        </th>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
            Jam Keluar
        </th>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
            Lampiran
        </th>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
            Status
        </th>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
            Status
        </th>

    </x-slot:thead>

    <x-slot:tbody>
        @foreach ($presences as $data)
        <tr>
            <td class="text-center">
                @can('presence-approve')
                <form action="{{ route('presences.approve', encrypt($data->id)) }}" method="POST" id="formApprove">
                    @csrf
                    @method('PUT')
                    <button id="button-accept-{{ $data->id }}"
                        class="btn button-presences {{ $data->is_approved ? 'btn-secondary' : 'btn-success' }} text-xs"
                        style="{{ $data->is_approved ? 'pointer-events: none' : '' }}" data-bs-toggle="tooltip"
                        data-bs-placement="bottom" title="Setujui" type="button"><i
                            class="bi bi-check-lg"></i></button>
                </form>
                @endcan
            </td>
            <td class="text-sm text-center">{{ $data->date }}</td>
            <td class="text-sm text-center">{{ $data->check_in }}</td>
            <td class="text-sm text-center">{{ $data->check_out }}</td>
            <td class="text-sm text-center">@if ($data->attachment)<img src="{{ $data->attachment }}" alt="" width="180">@endif</td>
            <td class="text-sm text-center">
                <span class="badge badge-sm" style="background-color: {{ $data->presenceStatus->color }}">
                    {{ $data->presenceStatus->name }}
            </td>
            <td class="text-sm text-center">
                <span
                    class="badge badge-sm @if ($data->is_approved) bg-gradient-success @else bg-gradient-danger @endif">
                    @if ($data->is_approved)
                    Disetujui
                    @else
                    Belum Disetujui
                    @endif
            </td>
        </tr>
        @endforeach
    </x-slot:tbody>
</x-table>
<div style="float:right">
    <a href="{{ route('students.index') }}" class="btn bg-gradient-info text-xs"
        data-bs-toggle="tooltip" data-bs-placement="bottom" title="Siswa">
        <i class="bi bi-arrow-left"></i></a>
</div>
@endsection

@push('scripts')
<script type="module">
    $(document).ready(function() {
        $('.button-presences').on('click', function() {
            const buttonId = $(this).attr('id');

            window
                .swal({
                    title: "Apakah anda yakin?",
                    text: "Anda akan menyetujui tindakan ini",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: "Batal",
                            value: null,
                            visible: true,
                            className: "btn btn-primary",
                            closeModal: true,
                        },
                        confirm: {
                            text: "Setuju",
                            value: true,
                            visible: true,
                            className: "btn btn-success",
                            closeModal: true,
                        },
                    },
                })
                .then((value) => {
                    if (value) {
                        $(`#${buttonId}`).closest('form').trigger('submit');
                    }
                });
        });
    });
</script>
@endpush