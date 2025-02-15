<div class="form-group has-validation" style="{{ $attributes->get('style') ? 'pointer-events: none;' : '' }}">

    @if ($label)
        <label for="input-data">{{ $label }}</label>
    @endif

    <input type="hidden" class="form-control" id="{{ $id }}" name="{{ $name }}"
        value="{{ $value ?? '' }}" @if ($disabled) style="pointer-events: none;" @endif />

    <!-- <trix-editor class="trix-content" input="{{ $id }}"></trix-editor> -->
    <textarea name="{{ $name }}" id="{{ $id }}" cols="30" rows="10" class="summernote" placeholder="{{ $placeholder ?? '' }}">{{ $value ?? '' }}</textarea>

    @error($name)
        <x-validation :message="$message" />
    @enderror
</div>

@once
    @push('scripts')
        <script type="module">
            $(document).ready(function() {
                $('.summernote').summernote({
                    height: 300,
                });
            })
        </script>
    @endpush
@endonce