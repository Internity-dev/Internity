@extends('layouts.dashboard')

@section('dashboard-content')
    <x-form.form formTitle="Tambah Pertanyaan" formMethod="POST" formAction="{{ route('faqs.store') }}">
        <x-slot:formBody>
            <x-form.input-base label="Pertanyaan" id="input-question" type="text" name="question" />
            <x-form.input-rich label="Jawaban" id="input-answer" name="answer" />
        </x-slot:formBody>
    </x-form.form>
@endsection
