@extends('layouts.dashboard')

@section('dashboard-content')
    <x-form.form formTitle="Edit Nilai" formMethod="POST" spoofMethod="PUT" formAction="{{ route('scores.update', encrypt($score->id)) }}" enctype="multipart/form-data">
        <x-slot:formBody>
            <x-form.input-base label="Subyek" id="input-name" type="text" name="name" value="{{ $score->name }}"/>
            <x-form.input-base label="Nilai" id="input-score" type="numeric" name="score" value="{{ $score->score }}"/>
            <div class="form-group">
                <label for="input-type">Tipe</label>
                <select name="type" id="input-type" class="form-control">
                    <option value="teknis" {{ $score->type == 'teknis' ? 'selected' : '' }}>Teknis</option>
                    <option value="non-teknis" {{ $score->type == 'non-teknis' ? 'selected' : '' }}>Non-Teknis</option>
                </select>
            </div>
        </x-slot:formBody>
    </x-form.form>
@endsection
