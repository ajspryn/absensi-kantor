@extends('layouts.admin')

@section('title', 'Kelola Lokasi Kantor - Admin')

@section('header')
    @include('admin.header', [
        'title' => 'Lokasi Kantor',
        'backUrl' => route('dashboard'),
        'rightHtml' => '<a href="' . route('admin.office-locations.create') . '"><i class="bi bi-plus-lg font-13 color-highlight"></i></a>',
    ])
@endsection

@section('content')
    @include('admin.partials.alerts')

    @include('admin.partials.section-header', [
        'title' => 'Lokasi Kantor',
        'subtitle' => 'Kelola lokasi untuk absensi karyawan',
        'icon' => 'bi bi-geo-alt',
    ])

    @include('admin.partials.filters', [
        'action' => route('admin.office-locations.index'),
        'method' => 'GET',
        'fields' => [['type' => 'select', 'name' => 'active', 'label' => 'Status', 'options' => ['1' => 'Aktif', '0' => 'Nonaktif'], 'col' => 6]],
        'submitLabel' => 'Terapkan',
    ])

    <!-- Location List -->
    @forelse ($locations as $location)
        <div class="card card-style location-item shadow-m entity-card {{ $location->is_active ? 'active' : 'inactive' }}">
            <div class="content">
                <div class="d-flex align-items-start">
                    <div class="align-self-center">
                        <div class="bg-{{ $location->is_active ? 'green' : 'gray' }}-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3 shadow-s" style="width: 50px; height: 50px;">
                            <i class="bi bi-building color-white font-16"></i>
                        </div>
                    </div>
                    <div class="align-self-center flex-grow-1">
                        <div class="d-flex align-items-center mb-1">
                            <h5 class="font-700 font-14 mb-0 me-2">{{ $location->name }}</h5>
                            <span class="badge bg-{{ $location->is_active ? 'green' : 'gray' }}-dark rounded-xl font-10 px-2 py-1">
                                {{ $location->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </div>
                        <p class="mb-1 font-11 opacity-70">
                            <i class="bi bi-geo-alt pe-1 color-blue-dark"></i>{{ Str::limit($location->address, 40) }}
                        </p>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-theme rounded-xl font-10 me-2 px-2 py-1">
                                <i class="bi bi-bullseye pe-1"></i>{{ $location->radius }}m
                            </span>
                            <span class="font-10 opacity-70">
                                {{ $location->latitude }}, {{ $location->longitude }}
                            </span>
                        </div>
                    </div>
                    <div class="align-self-center">
                        <div class="dropdown">
                            <button class="btn btn-sm bg-theme rounded-s dropdown-toggle shadow-bg shadow-bg-s d-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" style="width: 40px; height: 40px;">
                                <i class="bi bi-three-dots-vertical color-white font-14"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('admin.office-locations.show', $location) }}">
                                        <i class="bi bi-eye pe-2 color-blue-dark font-14"></i>
                                        <span class="font-12">Lihat Detail</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('admin.office-locations.edit', $location) }}">
                                        <i class="bi bi-pencil pe-2 color-green-dark font-14"></i>
                                        <span class="font-12">Edit Lokasi</span>
                                    </a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('admin.office-locations.toggle-status', $location) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="dropdown-item py-2 w-100 text-start border-0 bg-transparent">
                                            <i class="bi bi-toggle-{{ $location->is_active ? 'off' : 'on' }} pe-2 color-orange-dark font-14"></i>
                                            <span class="font-12">{{ $location->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('admin.office-locations.destroy', $location) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lokasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger py-2 w-100 text-start border-0 bg-transparent">
                                            <i class="bi bi-trash pe-2 color-red-dark font-14"></i>
                                            <span class="font-12">Hapus Lokasi</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        @include('admin.partials.empty', [
            'icon' => 'bi bi-geo-alt',
            'title' => request()->has('active') ? 'Tidak Ada Hasil' : 'Belum Ada Lokasi',
            'text' => request()->has('active') ? 'Tidak ditemukan lokasi dengan kriteria yang dipilih' : 'Tambahkan lokasi kantor untuk sistem absensi multi-lokasi.',
            'actionUrl' => !request()->has('active') ? route('admin.office-locations.create') : null,
            'actionLabel' => !request()->has('active') ? 'Tambah Lokasi Pertama' : null,
            'actionIcon' => 'bi bi-plus-lg',
        ])
    @endforelse

    <!-- Pagination -->
    @if ($locations->hasPages())
        <div class="card card-style">
            <div class="content">
                {{ $locations->links() }}
            </div>
        </div>
    @endif

    <!-- Floating Add Button -->
    <a href="{{ route('admin.office-locations.create') }}" class="btn btn-circle bg-highlight shadow-bg shadow-bg-s" style="position: fixed; bottom: 80px; right: 20px; width: 55px; height: 55px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-plus-lg color-white font-18"></i>
    </a>
@endsection
