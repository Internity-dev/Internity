{{-- @php
    dd($companies);
@endphp --}}

@extends('layouts.dashboard')

@section('title', 'Master Kuisioner')

@section('dashboard-content')
    <x-table routeCreate="{{ route('questions.create', ['school' => encrypt(1)]) }}"
        pageName="Master Kuisioner" permissionCreate="question-create" :pagination="$questions" :tableData="$questions" >

        <x-slot:thead>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
                No.
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
                Kelola
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-40">
                Pertanyaan
            </th>
        </x-slot:thead>

        <x-slot:tbody>
            @foreach ($questions as $data)
                <tr>
                    <td class="text-sm text-center">{{ $data->order }}</td>
                    <td class="d-flex align-items-center justify-content-center">
                        @can('score-predicate-edit')
                            <a href="{{ route('questions.edit', encrypt($data->id)) }}" class="btn btn-info text-xs me-1"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i
                                    class="bi bi-pencil-square"></i></a>
                        @endcan
                        @can('score-predicate-delete')
                            <form action="{{ route('questions.destroy', encrypt($data->id)) }}" method="POST" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button id="button-{{ $data->id }}" class="button-delete btn btn-danger text-xs ms-1"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete" type="button"><i
                                        class="bi bi-trash"></i></button>
                            </form>
                        @endcan
                    </td>
                    <td class="text-sm">{{ $data->question }}</td>
                </tr>
            @endforeach
        </x-slot:tbody>
    </x-table>
@endsection
