@extends('layouts.dashboard')

@section('dashboard-content')
<x-form.form formTitle="Edit Kompetensi Keahlian" formMethod="POST" spoofMethod="PUT"
    formAction="{{ route('departments.update', encrypt($department->id)) }}">
    <x-slot:formBody>
        <x-form.input-base label="Nama" id="input-name" type="text" name="name" placeholder="SIJA" value="{{ $department->name }}" />
        <x-form.input-base label="Deskripsi" id="input-description" type="text" name="description" placeholder="Sistem Informatika Jaringan dan Aplikasi" value="{{ $department->description }}" />
        <x-form.input-base label="Program Studi Keahlian" id="input-program" type="text" name="study_program" placeholder="Pengembangan Perangkat Lunak dan Gim" value="{{ $department->study_program }}" />
        <input hidden type="text" name="school_id" value="{{ $department->school_id }}">
    </x-slot:formBody>
</x-form.form>
@endsection