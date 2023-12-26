@extends('layouts.dashboard')

@section('dashboard-content')
    <x-form.form formTitle="Edit Siswa" formMethod="POST" spoofMethod="PUT"
        formAction="{{ route('students.update', ['id' => encrypt($student->id), 'company' => $company ? encrypt($company->id) : null]) }}">
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
            <x-form.input-base disabled label="IDUKA" id="input-companies" type="text" name="company"
                value="{{ $company?->name }}" />
            @if ($company)
                <x-form.input-base label="Tanggal Mulai" id="input-start-date" type="date" name="start_date"
                    value="{{ $student->internDates()->where('company_id', $company->id)->first()?->start_date }}" />
                <x-form.input-base label="Tanggal Selesai" id="input-end-date" type="date" name="end_date"
                    value="{{ $student->internDates()->where('company_id', $company->id)->first()?->end_date }}" />
                <x-form.input-base label="Lama Perpanjang (bulan)" id="input-extend" type="numeric" name="extend"
                    value="{{ $student->internDates()->where('company_id', $company->id)->first()?->extend }}" />
                <x-form.radio label="Status" name="finished">
                    <x-slot:checkboxItem>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="0" id="input-status-1"
                                name="finished"
                                {{ $student->internDates()->where('company_id', $company->id)->first()?->finished == 0? 'checked': '' }}>
                            <label class="form-check-label" for="input-status-1">Magang</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="1" id="input-status-2"
                                name="finished"
                                {{ $student->internDates()->where('company_id', $company->id)->first()?->finished == 1? 'checked': '' }}>
                            <label class="form-check-label" for="input-status-2">Selesai</label>
                        </div>
                    </x-slot:checkboxItem>
                </x-form.radio>
            @else
                <x-form.input-base disabled label="Tanggal Mulai" id="input-start-date" type="date" name="start_date"
                    value="" />
                <x-form.input-base disabled label="Tanggal Selesai" id="input-end-date" type="date" name="end_date"
                    value="" />
                <x-form.input-base disabled label="Lama Perpanjang (bulan)" id="input-extend" type="numeric" name="extend"
                    value="" />
            @endif
        </x-slot:formBody>
    </x-form.form>
@endsection
