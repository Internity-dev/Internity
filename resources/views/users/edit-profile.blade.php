@extends('layouts.dashboard')


@section('dashboard-content')
    <x-form.form formTitle="Edit User" formMethod="POST" spoofMethod="PUT"
        formAction="{{ route('users.updateProfile') }}" enctype="multipart/form-data">
        <x-slot:formBody>
            <x-form.input-base label="Nama *" id="input-name" type="text" name="name" value="{{ $user->name }}" />
            <x-form.input-base label="Email *" id="input-email" type="email" name="email" value="{{ $user->email }}" />

            <x-form.select label="Jenis Kelamin" id="input-gender" name="gender">
                <option selected hidden>Pilih</option>
                <x-slot:options>
                    <option value="male" {{ $user->gender == 'male' ? 'selected' : ''}}>Laki-laki</option>
                    <option value="female" {{ $user->gender == 'female' ? 'selected' : ''}}>Perempuan</option>
                </x-slot:options>
            </x-form.select>

            <x-form.input-base label="Avatar" id="input-avatar" type="file" name="avatar" placeholder="Avatar" value="{{ $user->avatar }}"/>
            <img src="{{ $user->avatar_url }}" alt="Avatar" style="width: 100px; object-fit: cover;">
        </x-slot:formBody>
    </x-form.form>
@endsection
