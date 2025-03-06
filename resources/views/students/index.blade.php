{{-- @php
    dd($students->toArray());
@endphp --}}

@extends('layouts.dashboard')

@section('title', 'Data PKL Siswa')

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
        $companies = $student->companies->filter(function($company) {
        if (auth()->user()->hasRole('mentor')) {
        return auth()->user()->companies->pluck('id')->contains($company->id);
        }
        return true;
        });
        @endphp
        <tr>
            <td class="text-center">
                @if ($companies->count() > 1)
                <div class="dropdown d-inline-block pb-2">
                    <button class="btn btn-info text-xs dropdown-toggle" type="button" id="actionDropdown{{ $student->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                        Aksi
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="actionDropdown{{ $student->id }}" style="min-width: fit-content;">
                        @foreach ($companies as $company)
                        @php
                        $internDate = $student->internDates()->where('company_id', $company->id)->first();
                        @endphp
                        <li>
                            <h6 class="dropdown-header">{{ $company->name }}</h6>
                            <a class="dropdown-item" href="{{ route('presences.index', ['user' => encrypt($student->id), 'company' => encrypt($company->id)]) }}" title="Presensi">
                                <i class="bi bi-calendar-check"></i> Presensi
                            </a>
                            <a class="dropdown-item" href="{{ route('journals.index', ['user' => encrypt($student->id), 'company' => encrypt($company->id)]) }}" title="Jurnal">
                                <i class="bi bi-journal-bookmark-fill"></i> Jurnal
                            </a>
                            @can('monitor-list')
                            <a class="dropdown-item" href="{{ route('monitors.index', ['user' => encrypt($student->id), 'company' => encrypt($company->id)]) }}" title="Monitoring">
                                <i class="bi bi-list-check"></i> Monitoring
                            </a>
                            @endcan
                            <a class="dropdown-item" href="{{ route('reviews.users.edit', ['user' => encrypt($student->id), 'company' => encrypt($company->id)]) }}" title="Review">
                                <i class="bi bi-chat-left-text"></i> Review
                            </a>
                            <a class="dropdown-item" href="{{ route('scores.index', ['user' => encrypt($student->id), 'company' => encrypt($company->id)]) }}" title="Nilai">
                                <i class="bi bi-award"></i> Nilai
                            </a>
                        </li>
                        <hr class="dropdown-divider">
                        @endforeach
                    </ul>
                </div>
                @else
                @foreach ($companies as $company)
                <a href="{{ route('presences.index', ['user' => encrypt($student->id), 'company' => encrypt($company->id)]) }}" class="btn btn-info text-xs" title="Presensi">
                    <i class="bi bi-calendar-check"></i>
                </a>
                <a href="{{ route('journals.index', ['user' => encrypt($student->id), 'company' => encrypt($company->id)]) }}" class="btn btn-info text-xs" title="Jurnal">
                    <i class="bi bi-journal-bookmark-fill"></i>
                </a>
                @can('monitor-list')
                <a href="{{ route('monitors.index', ['user' => encrypt($student->id), 'company' => encrypt($company->id)]) }}" class="btn btn-info text-xs" title="Monitoring">
                    <i class="bi bi-list-check"></i>
                </a>
                @endcan
                <a href="{{ route('reviews.users.edit', ['user' => encrypt($student->id), 'company' => encrypt($company->id)]) }}" class="btn btn-info text-xs" title="Review">
                    <i class="bi bi-chat-left-text"></i>
                </a>
                <a href="{{ route('scores.index', ['user' => encrypt($student->id), 'company' => encrypt($company->id)]) }}" class="btn btn-info text-xs" title="Nilai">
                    <i class="bi bi-award"></i>
                </a>
                @endforeach
                @endif
                <a href="{{ route('students.edit', ['id' => encrypt($student->id)]) }}" class="btn btn-info text-xs" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit">
                    <i class="bi bi-pencil-square"></i>
                </a>
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
            <td class="text-sm">
                @foreach ($companies as $company)
                <span>{{ $company->name }} </span>
                @endforeach
            </td>
            <td class="text-sm">
                @foreach ($companies as $company)
                @php
                $internDate = $student->internDates()->where('company_id', $company->id)->first();
                $startDate = $internDate?->start_date;
                @endphp
                <span>{{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d-m-Y') : 'Belum Diisi' }}</span>
                @endforeach
            </td>
            <td class="text-sm">
                @foreach ($companies as $company)
                @php
                $internDate = $student->internDates()->where('company_id', $company->id)->first();
                $endDate = $internDate?->end_date;
                @endphp
                <span>{{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d-m-Y') : 'Belum Diisi' }}</span>
                @endforeach
            </td>
            <td class="text-sm text-center">
                @foreach ($companies as $company)
                @php
                $internDate = $student->internDates()->where('company_id', $company->id)->first();
                @endphp
                <span>{{ $internDate?->extend }} bulan</span>
                @endforeach
            </td>
            <td class="text-sm text-center">
                @foreach ($companies as $company)
                @php
                $internDate = $student->internDates()->where('company_id', $company->id)->first();
                @endphp
                <span class="badge badge-sm bg-gradient-{{ $internDate?->finished ? 'success' : 'warning' }}">
                    {{ $internDate?->finished ? 'Selesai' : 'PKL' }}
                </span>
                @endforeach
            </td>
        </tr>
        @endforeach
    </x-slot:tbody>
</x-table>
@endsection