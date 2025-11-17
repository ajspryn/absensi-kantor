<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class FileController extends Controller
{
     /**
      * Get employee photo
      */
     public function getEmployeePhoto($filename)
     {
          $path = "public/employee_photos/{$filename}";

          if (!Storage::exists($path)) {
               return response()->json(['error' => 'File not found'], 404);
          }

          $file = Storage::get($path);
          $mimeType = Storage::mimeType($path);

          return Response::make($file, 200, [
               'Content-Type' => $mimeType,
               'Content-Disposition' => 'inline; filename="' . $filename . '"'
          ]);
     }

     /**
      * Get daily activity attachment
      */
     public function getDailyActivityAttachment($filename)
     {
          $path = "public/daily_activity_attachments/{$filename}";

          if (!Storage::exists($path)) {
               return response()->json(['error' => 'File not found'], 404);
          }

          $file = Storage::get($path);
          $mimeType = Storage::mimeType($path);

          return Response::make($file, 200, [
               'Content-Type' => $mimeType,
               'Content-Disposition' => 'inline; filename="' . $filename . '"'
          ]);
     }

     /**
      * Get attendance photo
      */
     public function getAttendancePhoto($filename)
     {
          $path = "public/attendance_photos/{$filename}";

          if (!Storage::exists($path)) {
               return response()->json(['error' => 'File not found'], 404);
          }

          $file = Storage::get($path);
          $mimeType = Storage::mimeType($path);

          return Response::make($file, 200, [
               'Content-Type' => $mimeType,
               'Content-Disposition' => 'inline; filename="' . $filename . '"'
          ]);
     }

     /**
      * Get correction attachment
      */
     public function getCorrectionAttachment($filename)
     {
          $path = "public/corrections/{$filename}";

          if (!Storage::exists($path)) {
               return response()->json(['error' => 'File not found'], 404);
          }

          $file = Storage::get($path);
          $mimeType = Storage::mimeType($path);

          return Response::make($file, 200, [
               'Content-Type' => $mimeType,
               'Content-Disposition' => 'inline; filename="' . $filename . '"'
          ]);
     }
}
