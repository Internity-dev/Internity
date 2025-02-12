{{-- @php
    dd($vacancies);
@endphp --}}

@extends('layouts.dashboard')

@section('dashboard-content')
<x-form.form formTitle="Edit Lowongan" formMethod="POST" spoofMethod="PUT"
    formAction="{{ route('vacancies.update', encrypt($vacancy->id)) }}">
    <x-slot:formBody>
        <x-form.input-base label="Nama" id="input-name" type="text" name="name" placeholder="Laravel Web Developer" value="{{ $vacancy->name }}" />
        <x-form.input-base label="Kategori" id="input-category" type="text" name="category"
            placeholder="IT" value="{{ $vacancy->category }}" />
        <x-form.input-base label="Skill Requirement (pisah dengan koma ',')" id="input-skills" type="text" name="skills" placeholder="HTML, CSS, JavaScript, PHP, Laravel, MySQL, Bootstrap" value="{{ $vacancy->skills }}" />
        <x-form.input-rich label="Deskripsi Pekerjaan" id="input-description" name="description"
            value="{!! $vacancy->description !!}" />
        <x-form.input-base label="Kuota" id="input-slots" type="number" min="0" name="slots"
            placeholder="3" value="{{ $vacancy->slots }}" />
        <x-form.radio label="Status" name="status">
            <x-slot:checkboxItem>
                <div class="form-check">
                    <input class="form-check-input" type="radio" value="1" id="input-status-1" name="status"
                        {{ $vacancy->status == 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="input-status-1">Aktif</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" value="0" id="input-status-2" name="status"
                        {{ $vacancy->status == 0 ? 'checked' : '' }}>
                    <label class="form-check-label" for="input-status-2">Inaktif</label>
                </div>
            </x-slot:checkboxItem>
        </x-form.radio>
    </x-slot:formBody>
</x-form.form>
@endsection

{{-- @once
    @push('scripts')
        <script type="module">
            axios.get('/departments/search?school=1')
                .then(response => {
                    console.log(response);
                })
                .catch(error => {
                    console.log(error);
                });
        </script>
    @endpush
@endonce --}}