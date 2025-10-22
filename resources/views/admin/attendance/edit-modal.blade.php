<div class="offcanvas offcanvas-end" tabindex="-1" id="editAttendanceSidebar" aria-labelledby="editAttendanceSidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="editAttendanceSidebarLabel">Edit Absensi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="editAttendanceForm" method="POST">
            @csrf
            <input type="hidden" name="attendance_id" id="attendance_id">
            <div class="mb-3">
                <label for="check_in" class="form-label">Check In</label>
                <input type="time" class="form-control" name="check_in" id="check_in">
            </div>
            <div class="mb-3">
                <label for="check_out" class="form-label">Check Out</label>
                <input type="time" class="form-control" name="check_out" id="check_out">
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
