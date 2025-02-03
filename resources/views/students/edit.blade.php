@extends('layouts.dashboard')

@section('dashboard-content')
    <x-form.form formTitle="Edit Siswa" formMethod="POST" spoofMethod="PUT" formAction="{{ route('students.update', ['id' => encrypt($student->id)]) }}">
        @error(['name', 'course_id', 'skills', 'company', 'start_date', 'end_date', 'extend'])
            <div class="alert alert-dark text-white help-block">{{ $message }}</div>
        @enderror
        <x-slot:formBody>
            @if (auth()->user()->can('user-edit'))
                <x-form.input-base label="Nama" id="input-name" type="text" name="name" required
                    value="{{ $student->name }}" />
                <x-form.select label="Kelas" id="input-course" required name="course_id">
                    <option selected hidden>Pilih</option>
                    <x-slot:options>
                        @foreach ($courses as $key => $value)
                            <option value="{{ $key }}"
                                {{ $student->courses()->first()?->id == $key ? 'selected' : '' }}>
                                {{ $value }}</option>
                        @endforeach
                    </x-slot:options>
                </x-form.select>
                <x-form.input-base label="Keahlian (pisahkan dengan koma ',')" id="input-skills" type="text"
                    name="skills" value="{{ $student->skills }}"
                    placeholder="HTML, CSS, JavaScript, PHP, Laravel, MySQL, Bootstrap" />
            @else
                <x-form.input-base readonly label="Nama" id="input-name" type="text" name="name" required
                    value="{{ $student->name }}" />
                <x-form.select readonly="true" label="Kelas" id="input-course" required name="course_id"
                    style="pointer-events: none;">
                    <option selected hidden>Pilih</option>
                    <x-slot:options>
                        @foreach ($courses as $key => $value)
                            <option value="{{ $key }}"
                                {{ $student->courses()->first()?->id == $key ? 'selected' : 'disabled' }}>
                                {{ $value }}</option>
                        @endforeach
                    </x-slot:options>
                </x-form.select>
                <x-form.input-base readonly label="Keahlian (pisahkan dengan koma ',')" id="input-skills" type="text"
                    name="skills" value="{{ $student->skills }}"
                    placeholder="HTML, CSS, JavaScript, PHP, Laravel, MySQL, Bootstrap" />
            @endif
            <div id="company-section">
            @foreach ($companiesData as $company)
            <div class="company-group mb-3 border p-3">
                <x-form.input-base disabled label="IDUKA" id="input-company-{{ $company['id'] }}" type="text" name="companies[{{ $company['id'] }}][name]" value="{{ $company['name'] }}" />

                <x-form.input-base label="Tanggal Mulai" id="input-start-date-{{ $company['id'] }}" type="date" name="companies[{{ $company['id'] }}][start_date]" value="{{ $company['start_date'] }}" />

                <x-form.input-base label="Tanggal Selesai" id="input-end-date-{{ $company['id'] }}" type="date" name="companies[{{ $company['id'] }}][end_date]" value="{{ $company['end_date'] }}" />

                <x-form.input-base label="Lama Perpanjang (bulan)" id="input-extend-{{ $company['id'] }}" type="number" name="companies[{{ $company['id'] }}][extend]" value="{{ $company['extend'] }}" />

                <x-form.radio label="Status" name="companies[{{ $company['id'] }}][finished]">
                    <x-slot:checkboxItem>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="0" id="input-status-0-{{ $company['id'] }}" name="companies[{{ $company['id'] }}][finished]" {{ $company['finished'] == 0 ? 'checked' : '' }}>
                            <label class="form-check-label" for="input-status-0-{{ $company['id'] }}">PKL</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="1" id="input-status-1-{{ $company['id'] }}" name="companies[{{ $company['id'] }}][finished]" {{ $company['finished'] == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="input-status-1-{{ $company['id'] }}">Selesai</label>
                        </div>
                    </x-slot:checkboxItem>
                </x-form.radio>
            </div>
            @endforeach
        </div>
        </x-slot:formBody>
    </x-form.form>
@endsection
