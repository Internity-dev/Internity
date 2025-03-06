@extends('layouts.dashboard')

@section('title', 'Master Status Kehadiran')

@section('dashboard-content')
    <x-table routeCreate="{{ route('presence-statuses.create', ['school'=>encrypt(1)]) }}" permissionCreate="presence-status-create" pageName="Master Status Kehadiran" :pagination="$presenceStatuses"
        :tableData="$presenceStatuses">

        <x-slot:thead>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Kelola
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-25">
                Nama
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-25">
                Deskripsi
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-25">
                Warna
            </th>
            {{-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Status
            </th> --}}
        </x-slot:thead>

        <x-slot:tbody>
            @foreach ($presenceStatuses as $data)
                <tr>
                    <td class="d-flex align-items-center justify-content-center">
                        <a href="{{ route('presence-statuses.edit', encrypt($data->id)) }}" class="btn btn-info text-xs me-1"
                            data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i
                                class="bi bi-pencil-square"></i></a>

                        <form action="{{ route('presence-statuses.destroy', encrypt($data->id)) }}" method="POST" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button id="button-{{ $data->id }}" class="button-delete btn btn-danger text-xs ms-1"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete" type="button"><i
                                    class="bi bi-trash"></i></button>
                        </form>
                    </td>
                    <td class="text-sm text-center">{{ $data->name }}</td>
                    <td class="text-sm text-center">{{ $data->description }}</td>
                    <td class="text-sm text-center"><button style="background-color: {{ $data->color }}; border:none; width:20px; height:20px; border-radius:20px" ></button></td>
                    {{-- <td class="text-sm text-center">{{ $data->status }}</td> --}}
                </tr>
            @endforeach
        </x-slot:tbody>
    </x-table>
@endsection
