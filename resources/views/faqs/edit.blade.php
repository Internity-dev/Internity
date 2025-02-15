@extends('layouts.dashboard')

@section('dashboard-content')
    <x-form.form formTitle="Edit Pertanyaan" formMethod="POST" spoofMethod="PUT" formAction="{{ route('faqs.update', encrypt($faq->id)) }}">
        <x-slot:formBody>
            <x-form.input-base label="Pertanyaan" id="input-question" type="text" name="question" value="{{ $faq->question }}" />
            <x-form.input-rich label="Jawaban" id="input-answer" name="answer" value="{!! $faq->answer !!}" />
        </x-slot:formBody>
    </x-form.form>
@endsection
