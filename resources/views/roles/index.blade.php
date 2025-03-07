{{-- @php
    dd($roles);
@endphp --}}

@extends('layouts.dashboard')

@section('title', 'Roles')

@section('dashboard-content')
    <x-table pageName="Roles" :pagination="$roles" :tableData="$roles">

        <x-slot:thead>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Kelola
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-15">
                Role
            </th>
            <!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-55">
                Permission
            </th> -->
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Status
            </th>
        </x-slot:thead>

        <x-slot:tbody>
            @foreach ($roles as $data)
                <tr>
                    <td class="text-center">
                        <a href="{{ route('roles.edit', encrypt($data->id)) }}" class="btn btn-info text-xs"
                            data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i
                                class="bi bi-pencil-square"></i></a>

                        {{-- <form action="{{ route('roles.destroy', encrypt($data->id)) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button id="button-{{ $data->id }}" class="button-delete btn btn-info text-xs"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete" type="button"><i
                                    class="bi bi-trash"></i></button>
                        </form> --}}
                    </td>
                    <td class="text-sm text-center">{{ $data->name }}</td>
                    <!-- <td class="text-sm">{{ $data->permissions->implode('name', ', ') }}</td> -->
                    <td class="text-center">
                        <p class="badge badge-sm {{ $data->status ? 'bg-gradient-success' : 'bg-gradient-danger' }}">{{ $data->status ? 'Aktif' : 'Inaktif' }}</p>
                    </td>
                </tr>
            @endforeach
        </x-slot:tbody>
    </x-table>
@endsection
