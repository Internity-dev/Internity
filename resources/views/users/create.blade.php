@extends('layouts.dashboard')

@section('dashboard-content')
    <x-form.form formTitle="Tambah User" formMethod="POST" formAction="{{ route('users.store') }}">
        <x-slot:formBody>
            <x-form.input-base label="Nama *" id="input-name" type="text" name="name" placeholder="John Doe"/>
            <x-form.input-base label="Email *" id="input-email" type="email" name="email" placeholder="johndoe@gmail.com"/>
            <x-form.input-password label="Password *" id="input-password" name="password" />
            <x-form.input-password label="Ulangi Password *" id="input-confirm-password" name="confirm-password" />
            <div class="form-group">
                <label class="form-label">Role *</label>
                <select class="form-select" name="role_id" id="input-role">
                    <option selected hidden value="">Pilih</option>
                    @foreach ($roles as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>

            <input type="hidden" value="1" name="school_id">

            <div id="department-wrapper" class="d-none">
                <x-form.select label="Kompetensi Keahlian *" id="input-department" name="department_id">
                    <option selected hidden>Pilih</option>
                    <x-slot:options>
                        @foreach ($departments as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </x-slot:options>
                </x-form.select>
            </div>

            <div id="course-wrapper" class="d-none">
                <x-form.select label="Kelas *" id="input-courses" name="course_id">
                    <option selected hidden>Pilih</option>
                    <x-slot:options>
                        @foreach ($courses as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </x-slot:options>
                </x-form.select>
            </div>

            <div id="company-wrapper" class="d-none">
                <x-form.select label="IDUKA *" id="input-companies" name="company_id">
                    <option selected hidden>Pilih</option>
                    <x-slot:options>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }} - {{ $company->department->name }}</option>
                        @endforeach
                    </x-slot:options>
                </x-form.select>
            </div>
        </x-slot:formBody>
    </x-form.form>
@endsection

@push('scripts')
    <script type="module">
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('input-role');
        const departmentWrapper = document.getElementById('department-wrapper');
        const courseWrapper = document.getElementById('course-wrapper');
        const companyWrapper = document.getElementById('company-wrapper');

        if (roleSelect && departmentWrapper) {
            roleSelect.addEventListener('change', function() {
                const selectedRole = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();

                if (selectedRole === 'kepala program' || selectedRole === 'kepala bengkel') {
                    departmentWrapper.classList.remove('d-none');
                    courseWrapper.classList.add('d-none');
                    companyWrapper.classList.add('d-none');
                } else if (selectedRole === 'mentor') {
                    departmentWrapper.classList.add('d-none');
                    companyWrapper.classList.remove('d-none');
                    courseWrapper.classList.add('d-none');
                } else if (selectedRole === 'student') {
                    departmentWrapper.classList.add('d-none');
                    companyWrapper.classList.add('d-none');
                    courseWrapper.classList.remove('d-none');
                } else if (selectedRole === 'teacher') {
                    departmentWrapper.classList.add('d-none');
                    companyWrapper.classList.add('d-none');
                    courseWrapper.classList.add('d-none');
                } else {
                    departmentWrapper.classList.add('d-none');
                    courseWrapper.classList.add('d-none');
                    companyWrapper.classList.add('d-none');
                }
            });
        } else {
            console.error('Elements not found');
        }
    });
    </script>
@endpush