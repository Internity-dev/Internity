{{-- @php
    dd($journals);
@endphp --}}
@extends('layouts.dashboard')

@section('dashboard-content')
    <x-table pageName="Jurnal PKL" :pagination="$journals" :tableData="$journals">

        <x-slot:thead>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Kelola
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Tanggal
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-15">
                Bidang Pekerjaan
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-50">
                Uraian Pekerjaan
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-14">
                Status
            </th>
        </x-slot:thead>

        <x-slot:tbody>
            @foreach ($journals as $data)
                <tr>
                    <td class="text-center">
                        @can('journal-approve')
                            <form action="{{ route('journals.approve', encrypt($data->id)) }}" method="POST" id="formApprove">
                                @csrf
                                @method('PUT')
                                {{-- <input type="hidden" name="is_approved" value="{{ $data->is_approved ? 0 : 1 }}"> --}}
                                <button id="button-accept-{{ $data->id }}"
                                    class="btn button-journals {{ $data->is_approved ? 'btn-secondary' : 'btn-success' }} text-xs"
                                    style="{{ $data->is_approved ? 'pointer-events: none' : '' }}" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="Ubah Status" type="button"><i
                                        class="bi bi-check-lg"></i></button>
                            </form>
                        @endcan
                    </td>
                    <td class="text-sm text-center">{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                    <td class="text-sm text-center">{{ $data->work_type }}</td>
                    <td class="text-sm">{{ $data->description }}</td>
                    <td class="text-sm text-center">
                        <p class="badge badge-sm {{ $data->is_approved ? 'bg-gradient-success' : 'bg-gradient-danger' }}">
                            {{ $data->is_approved ? 'Disetujui' : 'Belum disetujui' }}
                        </p>
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
        $('.button-journals').on('click', function() {
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
                    $(`#${buttonId}`).closest("form").trigger('submit');
                }
            });
        });
    });
</script>
@endpush
