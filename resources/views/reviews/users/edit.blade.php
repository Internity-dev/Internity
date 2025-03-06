@extends('layouts.dashboard')

@section('title', 'Ulasan ' . $user->name)

@section('dashboard-content')
    <x-form.form formTitle="Kuisioner {{ $user->name }}" formMethod="POST" spoofMethod="PUT" formAction="{{ route('reviews.users.update') }}" roleEdit="admin super-admin mentor manager">
        <x-slot:formBody>
            @foreach ($reviews as $review)
                <input type="hidden" name="reviews[id][]" value="{{ $review->id }}">
                <x-form.radio label="{{ $review->title }}" name="reviews[rating][]" value="{{ $review->rating }}">
                    <x-slot:checkboxItem>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="5"
                                id="input-review-5-{{ $review->id }}" {{ $review->rating == 5 ? 'checked' : '' }}
                                name="reviews[rating][{{ $review->id }}]">
                            <label class="form-check-label" for="input-review-5-{{ $review->id }}">Baik Sekali</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="4"
                                id="input-review-4-{{ $review->id }}" {{ $review->rating == 4 ? 'checked' : '' }}
                                name="reviews[rating][{{ $review->id }}]">
                            <label class="form-check-label" for="input-review-4-{{ $review->id }}">Baik</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="3"
                                id="input-review-3-{{ $review->id }}" {{ $review->rating == 3 ? 'checked' : '' }}
                                name="reviews[rating][{{ $review->id }}]">
                            <label class="form-check-label" for="input-review-3-{{ $review->id }}">Cukup</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="2"
                                id="input-review-2-{{ $review->id }}" {{ $review->rating == 2 ? 'checked' : '' }}
                                name="reviews[rating][{{ $review->id }}]">
                            <label class="form-check-label" for="input-review-2-{{ $review->id }}">Kurang</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="1"
                                id="input-review-1-{{ $review->id }}" {{ $review->rating == 1 ? 'checked' : '' }}
                                name="reviews[rating][{{ $review->id }}]">
                            <label class="form-check-label" for="input-review-1-{{ $review->id }}">Kurang Sekali</label>
                        </div>
                    </x-slot:checkboxItem>
                </x-form.radio>

                <x-form.input-base label="Saran" id="input-suggestion" type="text" name="reviews[body][]"
                    value="{{ $review->body }}" />
            @endforeach
        </x-slot:formBody>
    </x-form.form>
@endsection
