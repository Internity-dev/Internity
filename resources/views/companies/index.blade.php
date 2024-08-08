{{-- @php
    dd($companies);
@endphp --}}

@extends('layouts.dashboard')

@section('dashboard-content')
    <x-table routeCreate="{{ route('companies.create') }}" pageName="Perusahaan" permissionCreate="company-create" :pagination="$companies" :tableData="$companies">

        <x-slot:thead>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
                Kelola
            </th>
            @role('superadmin|admin|manager')
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-14">
                    Jurusan
                </th>
            @endrole
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-14">
                Nama
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
                Bidang Usaha
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-23">
                Alamat
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-14">
                Contact Person
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Email
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-16">
                Phone
            </th>
            @role('superadmin|admin|manager|teacher|student')
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
                    Rating
                </th>
            @endrole
        </x-slot:thead>

        <x-slot:tbody>
            @foreach ($companies as $data)
                <tr>
                    <td class="text-center">
                        @can('vacancy-list')
                            <a href="{{ route('vacancies.index', ['company' => encrypt($data->id)]) }}" class="btn btn-info text-xs position-relative" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Lowongan">
                                <i class="bi bi-person-workspace"></i>
                                @if ($data->vacancies->sum('pending_appliances_count') > 0)
                                    <span class="notification-dot"></span>
                                @endif
                            </a>
                        @endcan
                        @role('superadmin|admin|manager|teacher')
                            @can('review-list')
                                <a href="{{ route('reviews.companies.index', ['company' => encrypt($data->id)]) }}"
                                    class="btn btn-info text-xs" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                    title="Review"><i class="bi bi-chat-left-text"></i></a>
                            @endcan
                        @endrole
                        @can('company-edit')
                            <a href="{{ route('companies.edit', encrypt($data->id)) }}" class="btn btn-info text-xs"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i
                                    class="bi bi-pencil-square"></i></a>
                        @endcan
                        @can('company-delete')
                            <form action="{{ route('companies.destroy', encrypt($data->id)) }}" method="POST" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button id="button-{{ $data->id }}" class="button-delete btn btn-danger text-xs"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete" type="button"><i
                                        class="bi bi-trash"></i></button>
                            </form>
                        @endcan
                    </td>
                    @role('superadmin|admin|manager')
                        <td class="text-sm text-center">{{ $data->department->name }}</td>
                    @endrole
                    <td class="text-sm">{{ $data->name }}</td>
                    <td class="text-sm">{{ $data->category }}</td>
                    <td class="text-sm">{{ $data->address }}</td>
                    <td class="text-sm">{{ $data->contact_person }}</td>
                    <td class="text-sm">{{ $data->email }}</td>
                    <td class="text-sm">{{ $data->phone }}</td>
                    @php
                        $rating = number_format($data->reviews()?->avg('rating'), 1);
                    @endphp
                    @role('superadmin|admin|manager|teacher|student')
                        <td class="text-sm text-center">
                            @for($i = 0; $i < $rating; $i++)
                                <i class="bi bi-star-fill text-warning" style="font-size: 0.7rem;"></i>
                            @endfor
                            <span class="text-sm text-secondary" style="display: block;">{{ $rating }}</span>
                        </td>
                    @endrole
                </tr>
            @endforeach
        </x-slot:tbody>
    </x-table>
@endsection
