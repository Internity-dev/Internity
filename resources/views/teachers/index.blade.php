{{-- @php
    dd($teachers->toArray());
@endphp --}}

@extends('layouts.dashboard')

@section('dashboard-content')
    <x-table pageName="Data Guru" routeCreate="{{ route('users.create') }}" :pagination="$teachers" :tableData="$teachers">

        <x-slot:thead>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-15">
                Kelola
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-15">
                Nama
            </th>
        </x-slot:thead>

        <x-slot:tbody>
            @foreach ($teachers as $student)
                <tr>
                    <td class="text-center">
                        <a href="{{ route('teachers.edit', encrypt($student->id)) }}" class="btn btn-info text-xs"
                            data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit">
                            <i class="bi bi-pencil-square"></i></a>
                    </td>
                    <td class="text-sm">{{ $student->name }}</td>
                </tr>
            @endforeach
        </x-slot:tbody>
    </x-table>
@endsection
