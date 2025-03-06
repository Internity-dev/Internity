{{-- @php
    dd($companies);
@endphp --}}

@extends('layouts.dashboard')

@section('title', 'Kompetensi Keahlian')

@section('dashboard-content')
    <x-table routeCreate="{{ route('departments.create', ['school'=>encrypt($selectedSchool)]) }}" pageName="Kompetensi Keahlian"
        permissionCreate="department-create" :pagination="$departments" :tableData="$departments">

        <x-slot:thead>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Kelola
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-35">
                Nama
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-50">
                Deskripsi
            </th>
        </x-slot:thead>

        <x-slot:tbody>
            @foreach ($departments as $data)
                <tr>
                    <td class="text-center">
                        @can('course-list')
                            <a href="{{ route('courses.index', ['department' => encrypt($data->id)]) }}" class="btn btn-info text-xs"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Kelas"><i
                                    class="bi bi-book-half"></i></a>
                        @endcan
                        @can('department-edit')
                            <a href="{{ route('departments.edit', encrypt($data->id)) }}" class="btn btn-info text-xs"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i
                                    class="bi bi-pencil-square"></i></a>
                        @endcan
                        @can('department-delete')
                            <form action="{{ route('departments.destroy', encrypt($data->id)) }}" method="POST" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button id="button-{{ $data->id }}" class="button-delete btn btn-danger text-xs"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete" type="button"><i
                                        class="bi bi-trash"></i></button>
                            </form>
                        @endcan
                    </td>
                    <td class="text-sm text-center">{{ $data->name }}</td>
                    <td class="text-sm text-center">{{ $data->description }}</td>
            @endforeach
        </x-slot:tbody>
    </x-table>
@endsection
