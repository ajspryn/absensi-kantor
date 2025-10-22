@extends('layouts.admin')

@section('title', 'Import Karyawan Masal - Admin')

@section('header')
    @include('admin.header', [
        'title' => 'Import Karyawan Masal',
        'backUrl' => route('admin.employees.index'),
    ])
@endsection

@section('content')
    <!-- Instructions Card -->
    <div class="card card-style shadow-m mb-3">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-blue-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3 shadow-s" style="width: 45px; height: 45px;">
                    <i class="bi bi-file-earmark-excel-fill color-white font-18"></i>
                </div>
                <div>
                    <h4 class="font-700 mb-0 color-dark-dark">Import Karyawan dari Excel</h4>
                    <p class="mb-0 font-12 opacity-70">Upload file Excel untuk menambah karyawan secara masal</p>
                </div>
            </div>

            <div class="alert bg-info-dark text-white rounded-s mb-3">
                <div class="d-flex align-items-start">
                    <i class="bi bi-info-circle-fill me-3 font-16 mt-1"></i>
                    <div>
                        <h6 class="font-600 mb-2">Panduan Import:</h6>
                        <ol class="mb-0 font-12 ps-3">
                            <li>Download template Excel terlebih dahulu</li>
                            <li>Isi data karyawan sesuai format yang tersedia</li>
                            <li>Upload file Excel yang sudah diisi</li>
                            <li>Sistem akan memproses data dan memberikan laporan hasil</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Download Template -->
            <div class="mb-4">
                <h6 class="font-600 mb-3 color-green-dark">
                    <i class="bi bi-download me-2"></i>Download Template
                </h6>
                <p class="font-12 opacity-70 mb-3">Download template Excel untuk format yang benar:</p>
                <a href="{{ route('admin.employees.template') }}" class="btn btn-l gradient-green text-uppercase font-600 rounded-s shadow-bg shadow-bg-s">
                    <i class="bi bi-file-earmark-excel me-2"></i>Download Template Excel
                </a>
            </div>

            <div class="divider my-4"></div>

            <!-- Upload Form -->
            <div>
                <h6 class="font-600 mb-3 color-orange-dark">
                    <i class="bi bi-upload me-2"></i>Upload File Excel
                </h6>

                @include('admin.partials.alerts')

                @if ($errors->any())
                    <div class="alert bg-danger-dark text-white rounded-s mb-3">
                        <h6 class="font-600 mb-2">Terjadi kesalahan:</h6>
                        <ul class="mb-0 font-12">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.employees.import.process') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label font-600">Pilih File Excel <span class="color-red-dark">*</span></label>
                        <input type="file" class="form-control rounded-xl" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        <small class="form-text color-theme opacity-70">Format yang didukung: .xlsx, .xls, .csv (Max: 10MB)</small>
                    </div>

                    <button type="submit" class="btn btn-l gradient-orange text-uppercase font-600 rounded-s shadow-bg shadow-bg-s" id="importBtn">
                        <i class="bi bi-upload me-2"></i>Import Data Karyawan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Errors/Failures -->
    @if (session('failures') && count(session('failures')) > 0)
        <div class="card card-style shadow-m mb-3">
            <div class="content">
                <h5 class="font-700 mb-3 color-red-dark">
                    <i class="bi bi-exclamation-triangle me-2"></i>Data yang Gagal Diimport
                </h5>

                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Baris</th>
                                <th>Kolom</th>
                                <th>Kesalahan</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (session('failures') as $failure)
                                <tr>
                                    <td>{{ $failure->row() }}</td>
                                    <td>{{ $failure->attribute() }}</td>
                                    <td>{{ implode(', ', $failure->errors()) }}</td>
                                    <td>{{ $failure->values()[$failure->attribute()] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if (session('import_errors') && count(session('import_errors')) > 0)
        <div class="card card-style shadow-m mb-3">
            <div class="content">
                <h5 class="font-700 mb-3 color-red-dark">
                    <i class="bi bi-bug me-2"></i>Error Import
                </h5>

                <ul class="list-group">
                    @foreach (session('import_errors') as $error)
                        <li class="list-group-item list-group-item-danger">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Format Information -->
    <div class="card card-style shadow-m mb-3">
        <div class="content">
            <h5 class="font-700 mb-3 color-purple-dark">
                <i class="bi bi-table me-2"></i>Format Data Excel
            </h5>

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-primary">
                        <tr>
                            <th>Kolom</th>
                            <th>Wajib</th>
                            <th>Deskripsi</th>
                            <th>Contoh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>id_karyawan</td>
                            <td><span class="badge bg-danger">Ya</span></td>
                            <td>ID unik karyawan</td>
                            <td>EMP001</td>
                        </tr>
                        <tr>
                            <td>nama_lengkap</td>
                            <td><span class="badge bg-danger">Ya</span></td>
                            <td>Nama lengkap karyawan</td>
                            <td>John Doe</td>
                        </tr>
                        <tr>
                            <td>email</td>
                            <td><span class="badge bg-danger">Ya</span></td>
                            <td>Email unik karyawan</td>
                            <td>john@email.com</td>
                        </tr>
                        <tr>
                            <td>password</td>
                            <td><span class="badge bg-secondary">Tidak</span></td>
                            <td>Password login (default: password)</td>
                            <td>password123</td>
                        </tr>
                        <tr>
                            <td>departemen</td>
                            <td><span class="badge bg-secondary">Tidak</span></td>
                            <td>Nama departemen</td>
                            <td>IT</td>
                        </tr>
                        <tr>
                            <td>posisi</td>
                            <td><span class="badge bg-secondary">Tidak</span></td>
                            <td>Posisi/jabatan</td>
                            <td>Software Developer</td>
                        </tr>
                        <tr>
                            <td>telepon</td>
                            <td><span class="badge bg-secondary">Tidak</span></td>
                            <td>Nomor telepon</td>
                            <td>+6281234567890</td>
                        </tr>
                        <tr>
                            <td>alamat</td>
                            <td><span class="badge bg-secondary">Tidak</span></td>
                            <td>Alamat lengkap</td>
                            <td>Jl. Contoh No. 123</td>
                        </tr>
                        <tr>
                            <td>tanggal_masuk</td>
                            <td><span class="badge bg-secondary">Tidak</span></td>
                            <td>Tanggal mulai kerja</td>
                            <td>2024-01-15</td>
                        </tr>
                        <tr>
                            <td>gaji</td>
                            <td><span class="badge bg-secondary">Tidak</span></td>
                            <td>Gaji pokok (angka)</td>
                            <td>5000000</td>
                        </tr>
                        <tr>
                            <td>status_aktif</td>
                            <td><span class="badge bg-secondary">Tidak</span></td>
                            <td>Status aktif karyawan</td>
                            <td>aktif / tidak aktif</td>
                        </tr>
                        <tr>
                            <td>remote_attendance</td>
                            <td><span class="badge bg-secondary">Tidak</span></td>
                            <td>Izin absen remote</td>
                            <td>ya / tidak</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('importForm');
            const importBtn = document.getElementById('importBtn');
            const fileInput = document.getElementById('file');

            form.addEventListener('submit', function() {
                importBtn.disabled = true;
                importBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Memproses...';
            });

            // File validation
            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const fileSize = file.size / 1024 / 1024; // MB
                    const validTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                        'text/csv'
                    ];

                    if (fileSize > 10) {
                        alert('Ukuran file terlalu besar. Maksimal 10MB.');
                        this.value = '';
                        return;
                    }

                    if (!validTypes.includes(file.type)) {
                        alert('Format file tidak didukung. Gunakan .xlsx, .xls, atau .csv');
                        this.value = '';
                        return;
                    }
                }
            });
        });
    </script>
@endpush
