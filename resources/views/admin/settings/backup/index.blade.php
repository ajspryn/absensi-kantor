@extends('layouts.admin')

@section('title', 'Backup & Restore Database')

@section('header')
    @include('admin.header', [
        'title' => 'Backup Database',
        'backUrl' => route('dashboard'),
    ])
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-small rounded-s shadow-xl bg-green-dark mb-4" role="alert">
        <span><i class="bi bi-check-circle-fill"></i> Sukses</span>
        <strong>{{ session('success') }}</strong>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-small rounded-s shadow-xl bg-red-dark mb-4" role="alert">
        <span><i class="bi bi-exclamation-triangle-fill"></i> Gagal</span>
        <strong>{{ session('error') }}</strong>
    </div>
@endif

<div class="row">
    <div class="col-12 col-md-6 mb-4">
        <div class="card card-style">
            <div class="content">
                <div class="d-flex mb-3">
                    <div class="align-self-center">
                        <i class="bi bi-cloud-arrow-down color-blue-dark font-40 shadow-bg shadow-bg-s rounded-sm pb-1 pe-2"></i>
                    </div>
                    <div class="align-self-center ms-3">
                        <h4 class="mb-0">Backup Database</h4>
                        <p class="mb-0 text-muted font-12">Ekspor seluruh schema dan data ke format SQL terkompresi (.gz)</p>
                    </div>
                </div>
                
                <p>
                    Proses ini akan mengumpulkan struktur database dan data anda baris demi baris, 
                    serta langsung melakukan kompresi <code>ZLIB</code> tanpa membebankan ruang memori. Backup akan 
                    otomatis terunduh saat file siap.
                </p>

                <a href="{{ route('admin.database.backup.export') }}" class="btn btn-full btn-m font-900 text-uppercase rounded-sm shadow-l bg-blue-dark mt-4">
                    <i class="bi bi-download me-2"></i> Mulai Backup Data
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 mb-4">
        <div class="card card-style">
            <div class="content">
                <div class="d-flex mb-3">
                    <div class="align-self-center">
                        <i class="bi bi-cloud-arrow-up color-red-dark font-40 shadow-bg shadow-bg-s rounded-sm pb-1 pe-2"></i>
                    </div>
                    <div class="align-self-center ms-3">
                        <h4 class="mb-0">Restore Database</h4>
                        <p class="mb-0 text-muted font-12">Pemulihan data dari file (.gz / .sql)</p>
                    </div>
                </div>

                <div class="alert alert-small rounded-s shadow-xl bg-red-dark mb-4" role="alert">
                    <span><i class="bi bi-exclamation-triangle-fill"></i> Peringatan</span>
                    <strong>Sistem akan menghapus terlebih dahulu seluruh View & Table saat ini. Tindakan ini tidak bisa dibatalkan!</strong>
                </div>

                <form action="{{ route('admin.database.backup.import') }}" method="POST" enctype="multipart/form-data" id="restoreForm">
                    @csrf
                    <div class="mb-3">
                        <label for="backup_file" class="form-label font-12 font-600">Pilih file hasil backup (.sql / .gz)</label>
                        <input class="form-control" type="file" id="backup_file" name="backup_file" accept=".gz,.sql" required>
                        @error('backup_file')
                            <p class="text-danger font-11 mb-0 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" id="btnRestore" class="btn btn-full btn-m font-900 text-uppercase rounded-sm shadow-l bg-red-dark mt-4" 
                        onclick="return confirmRestore()">
                        <i class="bi bi-upload me-2" id="iconRestore"></i> <span id="textRestore">Mulai Restore Data</span>
                    </button>
                    <p id="loadingRestore" class="text-center font-11 mt-3 d-none text-muted">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Sedang memulihkan data. Proses ini mungkin memakan waktu, harap jangan tutup halaman...
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmRestore() {
    // Cek file terpilih
    const file = document.getElementById('backup_file').files[0];
    if (!file) {
        alert('Harap pilih file backup terlebih dahulu!');
        return false;
    }

    if (confirm('Apakah Anda benar-benar yakin ingin ME-WIPE dan merestore database saat ini? Aksi ini fatal dan tidak bisa dikembalikan.')) {
        document.getElementById('btnRestore').disabled = true;
        document.getElementById('btnRestore').classList.add('opacity-50');
        document.getElementById('iconRestore').classList.replace('bi-upload', 'bi-hourglass-split');
        document.getElementById('textRestore').innerText = 'Memproses...';
        document.getElementById('loadingRestore').classList.remove('d-none');
        document.getElementById('restoreForm').submit();
        return true;
    }
    return false;
}
</script>
@endsection