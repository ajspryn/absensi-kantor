@extends('layouts.admin')

@section('title', 'Detail Role')

@section('header')
    @include('admin.header', [
        'title' => 'Detail Role',
        'backUrl' => route('admin.roles.index'),
    ])
@endsection

@section('content')
    @include('admin.partials.alerts')
    <div class="card card-style bg-white shadow-xl mb-3">
        <div class="content">
            <h4 class="font-700 mb-2">{{ $role->name }}</h4>
            <p class="mb-2 color-theme">{{ $role->description ?? '-' }}</p>
            <div class="mb-2">
                <span class="badge bg-info">Level {{ $role->priority }}</span>
                @if ($role->is_active)
                    <span class="badge bg-success">Aktif</span>
                @else
                    <span class="badge bg-secondary">Nonaktif</span>
                @endif
                @if ($role->is_default)
                    <span class="badge bg-success">Default</span>
                @endif
            </div>
            <div class="mb-3">
                <strong>Permissions:</strong>
                <ul class="mt-2">
                    @foreach ($role->permissions as $permission)
                        <li class="font-12">{{ $permission }}</li>
                    @endforeach
                </ul>
            </div>
            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-full btn gradient-orange shadow-bg shadow-bg-s rounded-s font-700 text-uppercase">
                <i class="bi bi-pencil-square me-2"></i>Edit Role
            </a>
        </div>
    </div>
@endsection
