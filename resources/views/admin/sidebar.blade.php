<div id="admin-menu-main" class="offcanvas offcanvas-start offcanvas-detached rounded-m" style="width:280px;">
    <div class="content">
        <div class="d-flex pb-2">
            <div class="align-self-center">
                <h1 class="mb-0">Menu Admin</h1>
            </div>
            <div class="align-self-center ms-auto">
                <a href="#" class="ps-4" data-bs-dismiss="offcanvas">
                    <i class="bi bi-x color-red-dark font-26 line-height-xl"></i>
                </a>
            </div>
        </div>
        <div class="divider mb-2"></div>
        <a href="{{ route('admin.positions.index') }}" class="d-flex py-1">
            <div class="align-self-center">
                <i class="bi bi-briefcase color-blue-dark font-16"></i>
            </div>
            <div class="align-self-center ps-3">
                <h5 class="pt-1 mb-0">Posisi</h5>
            </div>
        </a>
        <a href="{{ route('admin.departments.index') }}" class="d-flex py-1">
            <div class="align-self-center">
                <i class="bi bi-building color-green-dark font-16"></i>
            </div>
            <div class="align-self-center ps-3">
                <h5 class="pt-1 mb-0">Departemen</h5>
            </div>
        </a>
        <a href="{{ route('admin.employees.index') }}" class="d-flex py-1">
            <div class="align-self-center">
                <i class="bi bi-people color-orange-dark font-16"></i>
            </div>
            <div class="align-self-center ps-3">
                <h5 class="pt-1 mb-0">Karyawan</h5>
            </div>
        </a>
        <div class="divider my-2"></div>
        <a href="{{ route('admin.positions.index') }}" class="d-flex py-1">
            <div class="align-self-center">
                <i class="bi bi-house-door color-theme font-16"></i>
            </div>
            <div class="align-self-center ps-3">
                <h5 class="pt-1 mb-0">Dashboard</h5>
            </div>
        </a>
    </div>
</div>
