@extends('layouts.dashboard')

@section('title', 'Kelas')

@section('dashboard-content')
    <x-table routeCreate="{{ route('courses.create', ['department'=> encrypt($departmentId)]) }}" pageName="Kelas" permissionCreate="course-create" :pagination="$courses" :tableData="$courses">
        <x-slot:thead>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-35">
                Kelola
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-15">
                Nama
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
                Kode Undangan
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-50">
                Deskripsi
            </th>
        </x-slot:thead>

        <x-slot:tbody>
            @foreach ($courses as $data)
                <tr>
                    <td class="d-flex justify-content-center">
                        @can('course-edit')
                            <a href="{{ route('courses.edit', encrypt($data->id)) }}" class="btn btn-info text-xs me 1"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i
                                    class="bi bi-pencil-square"></i></a>
                        @endcan
                        @can('course-delete')
                            <form action="{{ route('courses.destroy', encrypt($data->id)) }}" method="POST" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button id="button-{{ $data->id }}" class="button-delete btn btn-danger text-xs ms-1"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete" type="button"><i
                                        class="bi bi-trash"></i></button>
                            </form>
                        @endcan
                    </td>
                    <td class="text-sm text-center">{{ $data->name }}</td>
                    <td class="text-sm text-center">{{ $data->code->code }}</td>
                    <td class="text-sm text-center">{{ $data->description }}</td>
                </tr>
            @endforeach
        </x-slot:tbody>
    </x-table>
    <div style="float:right">

        <a href="{{ route('departments.index', ['school' => encrypt($data->id)]) }}" class="btn bg-gradient-info text-xs" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Kompetensi Keahlian">
            <i class="bi bi-arrow-left"></i></a>
    </div>
@endsection
