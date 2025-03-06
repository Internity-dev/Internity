<div class="alert {{ $type }} alert-dismissible fade show custom-alert" role="alert">
    {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<style>
    .custom-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
        width: auto;
        min-width: 250px;
        max-width: 400px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>