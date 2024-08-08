{{-- @php
    dd($vacancies);
@endphp --}}

@extends('layouts.dashboard')

@section('dashboard-content')
    <x-table routeCreate="{{ route('vacancies.create', ['company' => encrypt($company)]) }}" pageName="Lowongan"
        permissionCreate="vacancy-create" :pagination="$vacancies" :tableData="$vacancies">

        <x-slot:thead>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Kelola
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-25">
                Nama
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Kategori
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Keahlian
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-50">
                Deskripsi
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
                Kuota
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
                Pendaftar
            </th>
        </x-slot:thead>

        <x-slot:tbody>
            @foreach ($vacancies as $data)
                <tr>
                    <td class="text-center">
                        @can('appliance-list')
                            <a href="{{ route('appliances.index', ['vacancy' => encrypt($data->id)]) }}" class="btn btn-info text-xs position-relative" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Pendaftar">
                                <i class="bi bi-people"></i>
                                @if ($data->pending_appliances_count > 0)
                                    <span class="notification-dot"></span>
                                @endif
                            </a>
                        @endcan
                        @can('vacancy-edit')
                            <a href="{{ route('vacancies.edit', encrypt($data->id)) }}" class="btn btn-info text-xs"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i
                                    class="bi bi-pencil-square"></i></a>
                        @endcan
                        @can('vacancy-delete')
                            <form action="{{ route('vacancies.destroy', encrypt($data->id)) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button id="button-{{ $data->id }}" class="button-delete btn btn-danger text-xs"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete" type="button"><i
                                        class="bi bi-trash"></i></button>
                            </form>
                        @endcan
                    </td>
                    <td class="text-sm text-center">{{ $data->name }}</td>
                    <td class="text-sm text-center">{{ $data->category }}</td>
                    <td class="text-sm">
                        <ul>
                            @if ($data->skills)
                                @foreach (explode(",", $data->skills) as $skill)
                                    <li>{!! $skill !!}</li>
                                @endforeach
                            @endif
                        </ul>
                    </td>
                    <td class="text-sm">
                        <input type="hidden" id="rich-read-{{ $data->id }}" value="{!! $data->description !!}" />
                        <div id="blank-toolbar" hidden></div>
                        <trix-editor contenteditable=false toolbar="blank-toolbar" class="trix-content"
                            input="rich-read-{{ $data->id }}">
                        </trix-editor>
                    </td>
                    <td class="text-sm text-center">{{ $data->slots }}</td>
                    <td class="text-sm text-center">{{ $data->applied }}</td>
                </tr>
            @endforeach
        </x-slot:tbody>
    </x-table>
    <div style="float:right">
        <a href="{{ route('companies.index') }}" class="btn bg-gradient-info text-xs" data-bs-toggle="tooltip" data-bs-placement="bottom" title="IDUKA">
            <i class="bi bi-arrow-left"></i></a>
    </div>
@endsection
