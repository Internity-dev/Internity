<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="text-uppercase">{{ $pageName }}</h6>

                {{-- Table Function --}}
                <div
                    class="header-function d-flex align-items-center  @if ($routeCreate) justify-content-between @else justify-content-end @endif">
                    <!-- Add data Start -->
                    <div>
                    @if ($routeCreate)
                        <a href="{{ $routeCreate }}" class="btn bg-gradient-info mb-0">
                            TAMBAH DATA
                        </a>
                    @endif

                    @if (auth()->user()->name === 'Superadmin' && request()->is('students'))
                        <a href="{{ route('students.export', request()->query()) }}" class="btn bg-gradient-success mb-0">
                            CETAK DATA
                        </a>
                    @endif
                    </div>
                    <!-- Add data End -->

                    {{-- Filter Start --}}
                    @if (!empty($filter))
                        {{ $dropdown }}
                    @endif
                    {{-- Filter end --}}

                    <div class="card-header p-0">
                        <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                            <div class="input-group">
                                <span class="input-group-text text-body">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control search" placeholder="Cari..." name="search" id="search-input">
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Table --}}
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table id="table-data" class="table align-items-center mb-0">
                        <thead>
                            {{ $thead }}
                        </thead>
                        <tbody>
                            {{ $tbody }}
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                {{ $pagination ? $pagination->links() : '' }}
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script type="module">
            $('.button-delete').on('click', function() {
                const buttonId = $(this).attr('id');

                utils.useDeleteButton({
                    buttonId: buttonId
                });
            })

            // Add event listener for search input
            $('#search-input').on('input', function() {
                const searchQuery = $(this).val();
                const url = new URL(window.location.href);
                url.searchParams.set('search', searchQuery);
                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        const newDoc = new DOMParser().parseFromString(html, 'text/html');
                        const newTbody = newDoc.querySelector('#table-data tbody');
                        const newPagination = newDoc.querySelector('.pagination');
                        document.querySelector('#table-data tbody').innerHTML = newTbody.innerHTML;
                        if (newPagination) {
                            document.querySelector('.pagination').innerHTML = newPagination.innerHTML;
                        }
                    });
            });
        </script>
    @endpush


@endonce
