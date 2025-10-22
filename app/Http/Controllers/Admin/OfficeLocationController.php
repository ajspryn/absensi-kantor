<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OfficeLocation;

class OfficeLocationController extends Controller
{
     /**
      * Display a listing of office locations
      */
     public function index(Request $request)
     {
          $query = OfficeLocation::query();

          if ($request->has('active') && $request->get('active') !== '') {
               $query->where('is_active', $request->boolean('active'));
          }

          $locations = $query
               ->orderBy('created_at', 'desc')
               ->paginate(10)
               ->appends($request->query());

          return view('admin.office-locations.index', compact('locations'));
     }

     /**
      * Show the form for creating a new office location
      */
     public function create()
     {
          return view('admin.office-locations.create');
     }

     /**
      * Store a newly created office location
      */
     public function store(Request $request)
     {
          $request->validate([
               'name' => 'required|string|max:255',
               'address' => 'required|string',
               'latitude' => 'required|numeric|between:-90,90',
               'longitude' => 'required|numeric|between:-180,180',
               'radius' => 'required|integer|min:10|max:1000',
               'description' => 'nullable|string',
          ]);

          OfficeLocation::create([
               'name' => $request->name,
               'address' => $request->address,
               'latitude' => $request->latitude,
               'longitude' => $request->longitude,
               'radius' => $request->radius,
               'description' => $request->description,
               'is_active' => $request->has('is_active'),
          ]);

          return redirect()->route('admin.office-locations.index')
               ->with('success', 'Lokasi kantor berhasil ditambahkan.');
     }

     /**
      * Display the specified office location
      */
     public function show(OfficeLocation $officeLocation)
     {
          $officeLocation->load(['attendances' => function ($query) {
               $query->with('employee')->orderBy('date', 'desc')->limit(20);
          }]);

          $todayAttendances = $officeLocation->attendances()
               ->whereDate('date', today())
               ->with('employee')
               ->get();

          return view('admin.office-locations.show', compact('officeLocation', 'todayAttendances'));
     }

     /**
      * Show the form for editing the specified office location
      */
     public function edit(OfficeLocation $officeLocation)
     {
          return view('admin.office-locations.edit', compact('officeLocation'));
     }

     /**
      * Update the specified office location
      */
     public function update(Request $request, OfficeLocation $officeLocation)
     {
          $request->validate([
               'name' => 'required|string|max:255',
               'address' => 'required|string',
               'latitude' => 'required|numeric|between:-90,90',
               'longitude' => 'required|numeric|between:-180,180',
               'radius' => 'required|integer|min:10|max:1000',
               'description' => 'nullable|string',
          ]);

          $officeLocation->update([
               'name' => $request->name,
               'address' => $request->address,
               'latitude' => $request->latitude,
               'longitude' => $request->longitude,
               'radius' => $request->radius,
               'description' => $request->description,
               'is_active' => $request->has('is_active'),
          ]);

          return redirect()->route('admin.office-locations.index')
               ->with('success', 'Lokasi kantor berhasil diperbarui.');
     }

     /**
      * Remove the specified office location
      */
     public function destroy(OfficeLocation $officeLocation)
     {
          // Check if location has attendances
          if ($officeLocation->attendances()->count() > 0) {
               return redirect()->route('admin.office-locations.index')
                    ->with('error', 'Tidak dapat menghapus lokasi yang sudah memiliki data absensi.');
          }

          $officeLocation->delete();

          return redirect()->route('admin.office-locations.index')
               ->with('success', 'Lokasi kantor berhasil dihapus.');
     }

     /**
      * Toggle active status
      */
     public function toggleStatus(OfficeLocation $officeLocation)
     {
          $officeLocation->update([
               'is_active' => !$officeLocation->is_active
          ]);

          $status = $officeLocation->is_active ? 'diaktifkan' : 'dinonaktifkan';

          return redirect()->route('admin.office-locations.index')
               ->with('success', "Lokasi kantor berhasil {$status}.");
     }
}
