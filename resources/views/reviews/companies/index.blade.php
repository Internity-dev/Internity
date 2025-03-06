{{-- @php
    dd($companies);
@endphp --}}

@extends('layouts.dashboard')

@section('title', 'Ulasan ' . $companyName)

@section('dashboard-content')
    <x-table  pageName="Ulasan {{ $companyName }}"
        permissionCreate="review-create" :pagination="$reviews" :tableData="$reviews">

        <x-slot:thead>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Kelola
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
                Tanggal
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Nama
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Subyek
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Deskripsi
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
                Rating
            </th>
        </x-slot:thead>

        <x-slot:tbody>
            @foreach ($reviews as $data)
                <tr>
                    <td class="text-center">
                        {{-- @can('course-list')
                            <a href="{{ route('courses.index', ['department' => encrypt($data->id)]) }}" class="btn btn-info text-xs"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Kelas"><i
                                    class="bi bi-book-half"></i></a>
                        @endcan
                        @can('department-edit')
                            <a href="{{ route('reviews.edit', encrypt($data->id)) }}" class="btn btn-info text-xs"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i
                                    class="bi bi-pencil-square"></i></a>
                        @endcan
                        @can('department-delete')
                            <form action="{{ route('reviews.destroy', encrypt($data->id)) }}" method="POST" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button id="button-{{ $data->id }}" class="button-delete btn btn-danger text-xs"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete" type="button"><i
                                        class="bi bi-trash"></i></button>
                            </form>
                        @endcan --}}
                    </td>
                    <td class="text-sm text-center">{{ $data->created_at->format('d-m-Y') }}</td>
                    <td class="text-sm text-center">{{ $data->user->name }}</td>
                    <td class="text-sm">{{ $data->title }}</td>
                    <td class="text-sm">{{ $data->body }}</td>
                    <td class="text-sm text-center">
                        @for($i=0; $i < $data->rating; $i++)
                            <i class="bi bi-star-fill text-warning" style="font-size: 0.7rem"></i>
                        @endfor
                        <span class="text-sm text-center" style="display: block;">{{ $data->rating }}</span>
                    </td>
            @endforeach
        </x-slot:tbody>
    </x-table>
@endsection
