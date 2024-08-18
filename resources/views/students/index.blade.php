{{-- @php
    dd($students->toArray());
@endphp --}}

@extends('layouts.dashboard')

@section('dashboard-content')
    <x-table pageName="Data PKL Siswa" routeCreate="{{ route('users.create') }}" :pagination="$students" :tableData="$students">

        <x-slot:thead>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-15">
                Kelola
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-15">
                Nama
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
                Kelas
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Keahlian
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                IDUKA
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Tanggal Mulai
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
                Tanggal Selesai
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
                Perpanjang
            </th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-5">
                Status
            </th>
        </x-slot:thead>

        <x-slot:tbody>
            @foreach ($students as $student)
            @php
                $company = $student->companies()->first();
            @endphp
                @if (!$company)
                    <tr>
                        <td class="text-center">
                            <a href="{{ route('presences.index', ['user' => encrypt($student->id)]) }}"
                                class="btn btn-secondary text-xs" style="pointer-events: none" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="Presensi">
                                <i class="bi bi-calendar-check"></i></a>
                            <a href="{{ route('journals.index', ['user' => encrypt($student->id)]) }}"
                                class="btn btn-secondary text-xs" style="pointer-events: none" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="Jurnal">
                                <i class="bi bi-journal-bookmark-fill"></i></a>
                            @can('monitor-list')
                                <a href="{{ route('monitors.index', ['user' => encrypt($student->id)]) }}"
                                    class="btn btn-secondary text-xs" style="pointer-events: none" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="Monitoring">
                                    <i class="bi bi-list-check"></i></a>
                            @endcan
                            <a href="{{ route('students.edit', encrypt($student->id)) }}" class="btn btn-secondary text-xs"
                                style="pointer-events: none" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                title="Review">
                                <i class="bi bi-chat-left-text"></i></a>
                            <a href="{{ route('students.edit', encrypt($student->id)) }}" class="btn btn-secondary text-xs"
                                style="pointer-events: none" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                title="Nilai">
                                <i class="bi bi-award"></i></a>
                            <a href="{{ route('students.edit', encrypt($student->id)) }}" class="btn btn-info text-xs"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit">
                                <i class="bi bi-pencil-square"></i></a>
                        </td>
                        <td class="text-sm">{{ $student->name }}</td>
                        <td class="text-sm">{{ $student->courses()->first()?->name }}</td>
                        <td class="text-sm">
                            <ul>
                                @if ($student->skills)
                                    @foreach (explode(',', $student->skills) as $skill)
                                        <li>{{ $skill }}</li>
                                    @endforeach
                                @endif
                            </ul>
                        </td>
                        <td class="text-sm">{{ $student->companies()->first()?->name }}</td>
                        <td class="text-sm">{{ $student->internDates()->first()?->start_date }}</td>
                        <td class="text-sm">{{ $student->internDates()->first()?->end_date }}</td>
                        <td class="text-sm">{{ $student->internDates()->first()?->extend }}</td>
                    </tr>
                @else
                        @php
                            $startDate = $student
                                ->internDates()
                                ->where('company_id', $company->id)
                                ->first()?->start_date;
                            $endDate = $student
                                ->internDates()
                                ->where('company_id', $company->id)
                                ->first()?->end_date;
                        @endphp
                        <tr>
                            <td class="text-center">
                                <a href="{{ route('presences.index', ['user' => encrypt($student->id), 'company' => encrypt($company->id)]) }}"
                                    class="btn btn-info text-xs" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                    title="Presensi">
                                    <i class="bi bi-calendar-check"></i></a>
                                <a href="{{ route('journals.index', ['user' => encrypt($student->id), 'company' => encrypt($company->id)]) }}"
                                    class="btn btn-info text-xs" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                    title="Jurnal">
                                    <i class="bi bi-journal-bookmark-fill"></i></a>
                                @can('monitor-list')
                                    <a href="{{ route('monitors.index', ['user' => encrypt($student->id), 'company' => encrypt($company->id)]) }}"
                                        class="btn btn-info text-xs" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                        title="Monitoring">
                                        <i class="bi bi-list-check"></i></a>
                                @endcan
                                <a href="{{ route('reviews.users.edit', ['user' => encrypt($student->id), 'company' => encrypt($company->id)]) }}"
                                    class="btn btn-info text-xs" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                    title="Review">
                                    <i class="bi bi-chat-left-text"></i></a>
                                <a href="{{ route('scores.index', ['user' => encrypt($student->id), 'company' => encrypt($company->id)]) }}"
                                    class="btn btn-info text-xs" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                    title="Nilai">
                                    <i class="bi bi-award"></i></a>
                                <a href="{{ route('students.edit', ['id' => encrypt($student->id), 'company' => encrypt($company->id)]) }}"
                                    class="btn btn-info text-xs" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                    title="Edit">
                                    <i class="bi bi-pencil-square"></i></a>

                            </td>
                            <td class="text-sm">{{ $student->name }}</td>
                            <td class="text-sm">{{ $student->courses->first()?->name }}</td>
                            <td class="text-sm">
                                <ul>
                                    @if ($student->skills)
                                        @foreach (explode(',', $student->skills) as $skill)
                                            <li>{{ $skill }}</li>
                                        @endforeach
                                    @endif
                                </ul>
                            </td>
                            <td class="text-sm">{{ $company->name }}</td>
                            <td class="text-sm">
                                @if ($startDate)
                                    {{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }}
                                @else
                                    <p class="badge badge-sm bg-gradient-danger }}">Belum Diisi</p>
                                @endif
                            </td>
                            <td class="text-sm">
                                @if ($endDate)
                                    {{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}
                                @else
                                    <p class="badge badge-sm bg-gradient-danger }}">Belum Diisi</p>
                                @endif
                            <td class="text-sm text-center">
                                {{ $student->internDates()->where('company_id', $company->id)->first()?->extend }} bulan
                            </td>
                            <td class="text-sm text-center">
                                <span
                                    class="badge badge-sm bg-gradient-{{ $student->internDates()->where('company_id', $company->id)->first()?->finished? 'success': 'warning' }}">
                                    {{ $student->internDates()->where('company_id', $company->id)->first()?->finished? 'Selesai': 'PKL' }}
                                </span>
                            </td>
                        </tr>
                @endif
            @endforeach
        </x-slot:tbody>
    </x-table>
@endsection
