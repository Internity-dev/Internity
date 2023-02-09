{{-- @php
    dd($companies);
@endphp --}}

@extends('layouts.dashboard')

@section('dashboard-content')
    <x-table route="{{ route('score-predicates.create') }}" pageName="Master Predikat Nilai" :pagination="$scorePredicates">

        <x-slot:thead>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-30">
                Kelola
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-25">
                Nama
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
                Deskripsi
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-25">
                Warna
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Min
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Max
            </th>
        </x-slot:thead>

        <x-slot:tbody>
            @foreach ($scorePredicates as $data)
                <tr>
                    <td>
                        <a href="{{ route('score-predicates.edit', encrypt($data->id)) }}" class="btn btn-info text-xs"
                            data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i
                                class="bi bi-pencil-square"></i></a>
                        <a href="{{ route('score-predicates.destroy', encrypt($data->id)) }}" class="btn btn-info text-xs"
                            data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete"><i
                                class="bi bi-trash"></i></a>
                        {{-- <a href="{{ route('vacancies.index', ['company' => encrypt($data->id)]) }}"
                            class="btn btn-info text-xs" data-bs-toggle="tooltip" data-bs-placement="bottom"
                            title="Lowongan"><i class="bi bi-person-workspace"></i></a> --}}
                    </td>
                    <td class="text-sm">{{ $data->name }}</td>
                    <td class="text-sm">{{ $data->description }}</td>
                    <td class="text-sm">{{ $data->color }}</td>
                    <td class="text-sm">{{ $data->min }}</td>
                    <td class="text-sm">{{ $data->max }}</td>
                </tr>
            @endforeach
        </x-slot:tbody>
    </x-table>
@endsection