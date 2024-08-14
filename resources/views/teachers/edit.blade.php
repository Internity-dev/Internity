@extends('layouts.dashboard')

@section('dashboard-content')
    <x-form.form formTitle="Edit Guru" formMethod="POST" spoofMethod="PUT"
        formAction="{{ route('teachers.update', ['id' => encrypt($teacher->id)]) }}">
        @error(['name', 'course_id', 'skills', 'company', 'start_date', 'end_date', 'extend'])
            <div class="alert alert-dark text-white help-block">{{ $message }}</div>
        @enderror
        <x-slot:formBody>
            @if (auth()->user()->can('user-edit'))
                <x-form.input-base label="Nama" id="input-name" type="text" name="name" required
                    value="{{ $teacher->name }}" />
                <div id="student-wrapper" class="form-group">
                    <label class="form-label">Siswa Bimbingan</label>
                    <select class="form-select" style="width: 100%" name="student_ids[]" id="input-student" multiple="multiple">
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}" {{ in_array($student->id, $selectedStudentIds) ? 'selected' : '' }}>{{ $student->name }} - {{ $student->courses()->first()?->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <x-form.input-base readonly label="Nama" id="input-name" type="text" name="name" required
                    value="{{ $teacher->name }}" />
            @endif
        </x-slot:formBody>
    </x-form.form>
@endsection


@push('scripts')
    <script type="module">
        $(document).ready(function() {
            $("#input-student").select2();
        });
    </script>
@endpush