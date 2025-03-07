{{-- @php
    dd($permissions);
@endphp --}}

@extends('layouts.dashboard')


@section('dashboard-content')
<x-form.form formTitle="Tambah Role" formMethod="POST" formAction="{{ route('roles.store') }}">
    <x-slot:formBody>
        <x-form.input-base label="Nama" id="input-name" type="text" name="name" />

        <x-form.checkbox label="" name="permissions">
            <x-slot:checkboxItem>
                @foreach ($groupedPermissions as $group => $permissions)
                <div class="permission-group mb-4">
                    <h5>{{ ucfirst($group) }} Permissions</h5>
                    @foreach ($permissions as $permission)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="{{ $permission->id }}"
                            id="input-permission-{{ $permission->id }}" name="permissions[]">
                        <label class="form-check-label" for="input-permission-{{ $permission->id }}">
                            {{ $permission->name }}
                        </label>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </x-slot:checkboxItem>
        </x-form.checkbox>

        <x-form.radio label="Status" name="status">
            <x-slot:checkboxItem>
                <div class="form-check">
                    <input class="form-check-input" type="radio" value="1" id="input-status-1" name="status">
                    <label class="form-check-label" for="input-status-1">Aktif</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" value="0" id="input-status-2" name="status">
                    <label class="form-check-label" for="input-status-2">Inaktif</label>
                </div>
            </x-slot:checkboxItem>
        </x-form.radio>
    </x-slot:formBody>
</x-form.form>
@endsection