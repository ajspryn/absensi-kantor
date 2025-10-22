@extends('layouts.app')

@section('title', 'Permission Test')

@section('content')
    <div class="card card-style">
        <div class="content">
            <h3>Permission Test Page</h3>

            <h5>Current User: {{ auth()->user()->name ?? 'Guest' }}</h5>
            <h6>Role: {{ auth()->user()->role->name ?? 'None' }}</h6>

            <hr>

            <h5>Permission Tests:</h5>

            @canDo('employees.view')
            <p>✅ <strong>Can view employees</strong> - Button/menu akan tampil</p>
            @endCanDo

            @cannotDo('employees.view')
            <p>❌ <strong>Cannot view employees</strong> - Button/menu akan disembunyikan</p>
            @endCannotDo

            @canDo('employees.create')
            <p>✅ <strong>Can create employees</strong> - Create button akan tampil</p>
            @endCanDo

            @cannotDo('employees.create')
            <p>❌ <strong>Cannot create employees</strong> - Create button akan disembunyikan</p>
            @endCannotDo

            @canDo('roles.edit')
            <p>✅ <strong>Can edit roles</strong> - Role edit akan tampil</p>
            @endCanDo

            @cannotDo('roles.edit')
            <p>❌ <strong>Cannot edit roles</strong> - Role edit akan disembunyikan</p>
            @endCannotDo

            @hasRole('Admin')
                <p>🎯 <strong>User is Admin</strong> - Special admin features</p>
            @endHasRole

            @hasRole('Employee')
                <p>👤 <strong>User is Employee</strong> - Basic employee features</p>
            @endHasRole

            <hr>

            <h5>Multiple Permission Test:</h5>
            @hasAnyPermission(['employees.view', 'employees.create'])
                <p>✅ <strong>Has employee management permissions</strong></p>
            @endHasAnyPermission

            @hasAnyPermission(['roles.view', 'roles.edit'])
                <p>✅ <strong>Has role management permissions</strong></p>
            @endHasAnyPermission

            <div class="alert alert-info">
                <strong>Info:</strong> Tampilan di atas menunjukkan permission yang dimiliki user saat ini.
                Login dengan user berbeda untuk melihat perbedaan permission.
            </div>

            <a href="{{ route('dashboard') }}" class="btn btn-primary">Kembali ke Dashboard</a>
        </div>
    </div>
@endsection
