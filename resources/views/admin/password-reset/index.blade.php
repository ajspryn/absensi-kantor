@extends('layouts.admin')

@section('title', 'Kelola Reset Password - Admin Dashboard')

@section('header')
    @include('admin.header', [
        'title' => 'Kelola Reset Password',
        'backUrl' => route('dashboard'),
    ])
@endsection

@section('footer')
@endsection

@section('content')
    <!-- Statistics Card -->
    <div class="card card-style">
        <div class="content">
            <div class="d-flex align-items-center">
                <div class="bg-orange-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                    <i class="bi bi-key color-white font-20"></i>
                </div>
                <div class="flex-grow-1">
                    <h4 class="font-700 mb-1 font-18">Permintaan Reset Password</h4>
                    <p class="mb-0 font-13 opacity-70">Total: {{ $resetRequests->total() }} permintaan</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-orange-dark font-12 px-3 py-2">
                        {{ $resetRequests->where('status', 'pending')->count() }} Pending
                    </span>
                </div>
            </div>
        </div>
    </div>

    @include('admin.partials.alerts')

    <!-- Reset Requests List -->
    @foreach ($resetRequests as $request)
        <div class="card card-style">
            <div class="content">
                <div class="d-flex align-items-start">
                    <div class="align-self-center me-3">
                        <div class="bg-{{ $request->status === 'pending' ? 'orange' : ($request->status === 'approved' ? 'green' : 'red') }}-light rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-{{ $request->status === 'pending' ? 'clock' : ($request->status === 'approved' ? 'check-circle' : 'x-circle') }} color-{{ $request->status === 'pending' ? 'orange' : ($request->status === 'approved' ? 'green' : 'red') }}-dark font-18"></i>
                        </div>
                    </div>
                    <div class="align-self-center flex-grow-1">
                        <h6 class="mb-1 font-15">{{ $request->email }}</h6>
                        <p class="mb-2 font-12 opacity-70">{{ $request->created_at->diffForHumans() }}</p>

                        @if ($request->reason)
                            <div class="bg-info-dark p-2 rounded-s mb-2 text-white">
                                <small class="color-blue-dark"><strong>Alasan:</strong> {{ $request->reason }}</small>
                            </div>
                        @endif

                        <span class="badge bg-{{ $request->status === 'pending' ? 'orange' : ($request->status === 'approved' ? 'green' : 'red') }}-dark font-11 px-3 py-2">
                            @if ($request->status === 'pending')
                                Menunggu Persetujuan
                            @elseif($request->status === 'approved')
                                Disetujui {{ $request->approved_at ? $request->approved_at->diffForHumans() : '' }}
                            @elseif($request->status === 'rejected')
                                Ditolak {{ $request->approved_at ? $request->approved_at->diffForHumans() : '' }}
                            @else
                                Sudah Digunakan
                            @endif
                        </span>

                        @if ($request->approver)
                            <small class="d-block mt-1 opacity-70">
                                oleh: {{ $request->approver->name }}
                            </small>
                        @endif
                    </div>
                </div>

                @if ($request->status === 'pending')
                    <div class="divider my-3"></div>
                    <div class="row g-2">
                        <div class="col-6">
                            <form method="POST" action="{{ route('admin.password-reset.approve', $request->id) }}" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-full btn-m bg-green-dark text-uppercase font-700 rounded-s" onclick="return confirm('Setujui permintaan reset password ini?')">
                                    <i class="bi bi-check-circle pe-2"></i>Setujui
                                </button>
                            </form>
                        </div>
                        <div class="col-6">
                            <form method="POST" action="{{ route('admin.password-reset.reject', $request->id) }}" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-full btn-m bg-red-dark text-uppercase font-700 rounded-s" onclick="return confirm('Tolak permintaan reset password ini?')">
                                    <i class="bi bi-x-circle pe-2"></i>Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                @elseif($request->status === 'approved' && !$request->isExpired())
                    <div class="divider my-3"></div>
                    <div class="alert bg-success-dark text-white rounded-s mb-0" role="alert">
                        <small>
                            <i class="bi bi-info-circle pe-1"></i>
                            <strong>Link reset:</strong> {{ route('password.reset', $request->token) }}
                        </small>
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    <!-- Pagination -->
    @if ($resetRequests->hasPages())
        <div class="card card-style">
            <div class="content">
                {{ $resetRequests->links() }}
            </div>
        </div>
    @endif

    @if ($resetRequests->isEmpty())
        <div class="card card-style">
            <div class="content text-center py-5">
                <i class="bi bi-inbox color-theme font-40 d-block mb-3"></i>
                <h5 class="color-theme mb-2">Belum Ada Permintaan</h5>
                <p class="font-13 opacity-70 mb-0">Belum ada permintaan reset password dari user</p>
            </div>
        </div>
    @endif
@endsection
