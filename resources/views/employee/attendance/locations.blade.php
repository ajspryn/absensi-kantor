@extends('layouts.app')

@section('sidebar')
    @include('employee.sidebar')
@endsection
@section('footer')
    @include('employee.footer')
@endsection

@section('title', 'Lokasi Kantor - Aplikasi Absensi')

@section('header')
    <!-- Header -->
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('dashboard') }}"><i class="bi bi-arrow-left font-16 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-15">Lokasi Kantor</a>
        <a href="#"><i class="bi bi-house font-16 color-theme"></i></a>
    </div>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="card card-style">
        <div class="content">
            <div class="text-center">
                <div class="bg-theme rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px;">
                    <i class="bi bi-geo-alt color-white font-20"></i>
                </div>
                <h4 class="font-700 mb-1 font-16">Lokasi Kantor Yang Tersedia</h4>
                <p class="mb-0 opacity-70 font-12">Anda dapat melakukan absensi di lokasi-lokasi berikut</p>
            </div>
        </div>
    </div>

    <!-- Office Locations List -->
    @if ($officeLocations->count() > 0)
        @foreach ($officeLocations as $location)
            <div class="card card-style">
                <div class="content mb-0">
                    <div class="d-flex">
                        <div class="align-self-center">
                            <div class="bg-{{ $location->is_active ? 'green' : 'gray' }}-dark rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                <i class="bi bi-building text-white font-15"></i>
                            </div>
                        </div>
                        <div class="align-self-center flex-grow-1">
                            <h6 class="font-600 mb-1 font-15">{{ $location->name }}</h6>
                            <p class="mb-1 font-11 opacity-70">{{ $location->address }}</p>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{ $location->is_active ? 'green' : 'gray' }}-light me-2 font-9 px-1 py-0 color-{{ $location->is_active ? 'green' : 'gray' }}-dark">
                                    {{ $location->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                                <small class="font-9 opacity-70">
                                    <i class="bi bi-bullseye pe-1"></i>Radius: {{ $location->radius }}m
                                </small>
                            </div>
                        </div>
                        <div class="align-self-center">
                            <button type="button" class="btn btn-s bg-theme text-white rounded-s" onclick="showLocationDetails({{ $location->id }}, '{{ $location->name }}', {{ $location->latitude }}, {{ $location->longitude }}, {{ $location->radius }})" style="min-height: 35px;">
                                <i class="bi bi-eye font-11"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <!-- No Locations -->
        <div class="card card-style">
            <div class="content text-center">
                <div class="bg-gray-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-exclamation-triangle text-white font-20"></i>
                </div>
                <h5 class="font-600 mb-2 font-15">Belum Ada Lokasi</h5>
                <p class="mb-0 opacity-70 font-12">Belum ada lokasi kantor yang tersedia. Hubungi admin untuk informasi lebih lanjut.</p>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        function showLocationDetails(id, name, lat, lng, radius) {
            // Create a modal to show location details
            const modal = `
            <div class="modal fade" id="locationModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content bg-white">
                        <div class="modal-header border-0">
                            <h5 class="modal-title font-600 font-15">${name}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-3">
                                <div class="bg-theme rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                    <i class="bi bi-geo-alt text-white font-18"></i>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="bg-gray-light rounded-s p-2 text-center">
                                        <small class="font-10 opacity-70 d-block">Latitude</small>
                                        <strong class="font-12">${lat}</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-gray-light rounded-s p-2 text-center">
                                        <small class="font-10 opacity-70 d-block">Longitude</small>
                                        <strong class="font-12">${lng}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="alert bg-blue-dark color-white rounded-s mt-2 mb-0 py-1" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-info-circle pe-2 font-12"></i>
                                    <div>
                                        <small class="font-11">Anda dapat melakukan absensi dalam radius <strong>${radius} meter</strong> dari titik ini.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-sm bg-gray-dark text-white rounded-s me-2" data-bs-dismiss="modal">Tutup</button>
                            <button type="button" class="btn btn-sm bg-theme text-white rounded-s" onclick="openMaps(${lat}, ${lng})">Buka Maps</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

            // Remove existing modal if any
            const existingModal = document.getElementById('locationModal');
            if (existingModal) {
                existingModal.remove();
            }

            // Add modal to DOM
            document.body.insertAdjacentHTML('beforeend', modal);

            // Show modal
            const modalElement = new bootstrap.Modal(document.getElementById('locationModal'));
            modalElement.show();
        }

        function openMaps(lat, lng) {
            const url = `https://www.google.com/maps?q=${lat},${lng}`;
            window.open(url, '_blank');
        }
    </script>
@endpush
