@extends('layouts.dashboard')

@section('dashboard-content')
    <x-table routeCreate="{!! route('news.create', ['category' => $category, 'school' => encrypt($selectedSchool->id)]) !!}"
        pageName="Berita & Artikel {{ $category == 'school' ? $selectedSchool->name : $selectedDepartment->name }}"
        permissionCreate="news-create" roleCreate="superadmin admin manager" showButton="{{ $category == 'department' ? true : false }}" :pagination="$news" :tableData="$news"
        filter="{{ $category == 'department' ? true : false }}">

        @if ($category == "department")
            <x-slot:dropdown>
                <x-dropdown-filter>
                    <x-slot:dropdownItems>
                        @foreach ($departments as $key => $department)
                            <a href="{{ route('news.index', ['category' => 'department', 'school' => encrypt($selectedSchool->id), 'department' => encrypt($key)]) }}"
                                class="dropdown-item">{{ $department }}</a>
                        @endforeach
                    </x-slot:dropdownItems>
                </x-dropdown-filter>
            </x-slot:dropdown>
        @endif

        <x-slot:thead>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
                Kelola
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-15">
                Judul
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Gambar
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-25">
                Konten
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
                Penulis
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
                Status
            </th>
        </x-slot:thead>

        <x-slot:tbody>
            @foreach ($news as $data)
                <tr>
                    <td class="text-center">
                        @if ($category != 'department')
                            @role('super-admin|admin|manager')
                                @can('news-edit')
                                    <a href="{{ route('news.edit', encrypt($data->id)) }}" class="btn btn-info text-xs"
                                        data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i
                                            class="bi bi-pencil-square"></i></a>
                                @endcan
                                @can('news-delete')
                                    <form action="{{ route('news.destroy', encrypt($data->id)) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button id="button-{{ $data->id }}" class="button-delete btn btn-danger text-xs"
                                            data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete" type="button"><i
                                                class="bi bi-trash"></i></button>
                                    </form>
                                @endcan
                            @endrole
                        @else
                            @can('news-edit')
                                <a href="{{ route('news.edit', encrypt($data->id)) }}" class="btn btn-info text-xs"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i
                                        class="bi bi-pencil-square"></i></a>
                            @endcan
                            @can('news-delete')
                                <form action="{{ route('news.destroy', encrypt($data->id)) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button id="button-{{ $data->id }}" class="button-delete btn btn-danger text-xs"
                                        data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete" type="button"><i
                                            class="bi bi-trash"></i></button>
                                </form>
                            @endcan
                        @endif

                    </td>
                    <td class="text-sm text-center">{{ $data->title }}</td>
                    <td class="text-sm text-center">
                        @if ($data->image)
                            <img src="{{ url($data->image) }}" alt="image" class="img-fluid" width="100">
                        @endif
                    </td>
                    <td class="text-sm">
                        <input type="hidden" id="rich-read-{{ $data->id }}" value="{!! $data->content !!}" />
                        <div id="blank-toolbar" hidden></div>
                        <trix-editor contenteditable=false toolbar="blank-toolbar" class="trix-content"
                            input="rich-read-{{ $data->id }}">
                        </trix-editor>
                    </td>
                    <td class="text-sm text-center">{{ $data->user->name }}</td>
                    <td class="text-sm text-center">
                        @if ($data->status == 1)
                            <span class="badge badge-sm bg-gradient-success">Aktif</span>
                        @else
                            <span class="badge badge-sm bg-gradient-danger">Tidak Aktif</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </x-slot:tbody>
    </x-table>
@endsection
