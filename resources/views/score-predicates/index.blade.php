{{-- @php
    dd($companies);
@endphp --}}

@extends('layouts.dashboard')

@section('dashboard-content')
    <x-table routeCreate="{{ route('score-predicates.create', ['school' => Crypt::encrypt(1)]) }}"
        pageName="Master Predikat Nilai" permissionCreate="score-predicate-create" :pagination="$scorePredicates" :tableData="$scorePredicates" >

        <x-slot:thead>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-25">
                Kelola
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Nama
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-20">
                Deskripsi
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-15">
                Warna
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-15">
                Min
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-15">
                Max
            </th>
        </x-slot:thead>

        <x-slot:tbody>
            @foreach ($scorePredicates as $data)
                <tr>
                    <td class="d-flex align-items-center justify-content-center">
                        @can('score-predicate-edit')
                            <a href="{{ route('score-predicates.edit', encrypt($data->id)) }}" class="btn btn-info text-xs me-1"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i
                                    class="bi bi-pencil-square"></i></a>
                        @endcan
                        @can('score-predicate-delete')
                            <form action="{{ route('score-predicates.destroy', encrypt($data->id)) }}" method="POST" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button id="button-{{ $data->id }}" class="button-delete btn btn-danger text-xs ms-1"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete" type="button"><i
                                        class="bi bi-trash"></i></button>
                            </form>
                        @endcan
                    </td>
                    <td class="text-sm text-center">{{ $data->name }}</td>
                    <td class="text-sm text-center">{{ $data->description }}</td>
                    <td class="text-sm text-center"><button style="background-color: {{ $data->color}}; border:none; height:20px; border-radius:20px; width:20px;"></button></td>
                    <td class="text-sm text-center">{{ $data->min }}</td>
                    <td class="text-sm text-center">{{ $data->max }}</td>
                </tr>
            @endforeach
        </x-slot:tbody>
    </x-table>
@endsection
