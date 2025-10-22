@if (session('success'))
    <div class="alert bg-success-dark alert-dismissible text-white rounded-s fade show pe-2 mb-3" role="alert">
        <strong>Success:</strong> {{ session('success') }}
        <button type="button" class="btn-close opacity-20 font-11 pt-3 mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert bg-danger-dark alert-dismissible text-white rounded-s fade show pe-2 mb-3" role="alert">
        <strong>Error:</strong> {{ session('error') }}
        <button type="button" class="btn-close opacity-20 font-11 pt-3 mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert bg-danger-dark alert-dismissible text-white rounded-s fade show pe-2 mb-3" role="alert">
        <strong>Periksa kembali input Anda:</strong>
        <ul class="mb-0 mt-2 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close opacity-20 font-11 pt-3 mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
