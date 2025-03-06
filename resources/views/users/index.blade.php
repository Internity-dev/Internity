{{-- @php
    dd($users);
@endphp --}}


@extends('layouts.dashboard')

@section('title', 'Data ' . (auth()->user()->hasRole('kepala bengkel') ? 'Mentor' : 'User'))

@section('dashboard-content')
<x-table routeCreate="{{ route('users.create') }}" permissionCreate="user-create" pageName="{{ auth()->user()->hasRole('kepala bengkel') ? 'mentor' : 'user' }}" :pagination="$users"
    :tableData="$users">

    <x-slot:thead>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
            Kelola
        </th>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-25">
            Nama
        </th>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-15">
            Email
        </th>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-15">
            Role
        </th>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-15">
            Last Login
        </th>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-15">
            Last Login IP
        </th>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-15">
            Status
        </th>
    </x-slot:thead>

    <x-slot:tbody>
        @foreach ($users as $user)
        <tr>
            <td class="d-flex align-items-center justify-content-center">
                @can('user-edit')
                <a href="{{ route('users.edit', encrypt($user->id)) }}" class="btn btn-info text-xs me-1"
                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i
                        class="bi bi-pencil-square"></i></a>
                <form action="{{ route('users.updateStatus', encrypt($user->id)) }}" method="POST" class="m-0">
                    @csrf
                    @method('PUT')
                    <button id="button-{{ $user->id }}"
                        class="button-status btn btn-{{ $user->status == 1 ? 'danger' : 'success' }} text-xs ms-1"
                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                        title="{{ $user->status == 1 ? 'Nonaktifkan' : 'Aktifkan' }}" type="submit"><i
                            class="bi bi-{{ $user->status == 1 ? 'x-circle' : 'check-circle' }}"></i></button>
                </form>
                @endcan
                @can('user-delete')
                <form action="{{ route('users.destroy', encrypt($user->id)) }}" method="POST" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button id="delete-button-{{ $user->id }}" class="button-delete btn btn-danger text-xs ms-1"
                        data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete" type="button"><i
                            class="bi bi-trash"></i></button>
                </form>
                @endcan
            </td>
            <td class="text-sm text-center">{{ $user->name }}</td>
            <td class="text-sm text-center">{{ $user->email }}</td>
            <td class="text-sm text-center">{{ $user->roles()->first()->name }}</td>
            <td class="text-sm text-center">{{ $user->last_login?->format('d-m-Y H:i:s') }}</td>
            <td class="text-sm text-center">{{ $user->last_login_ip }}</td>
            <td class="text-sm text-center">
                <span
                    class="badge badge-sm bg-gradient-{{ $user->status == 1 ? 'success' : 'danger' }}">{{ $user->status == 1 ? 'Aktif' : 'Nonaktif' }}</span>
            </td>
        </tr>
        @endforeach
    </x-slot:tbody>
</x-table>
@endsection