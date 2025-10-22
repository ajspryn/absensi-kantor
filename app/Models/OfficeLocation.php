<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OfficeLocation extends Model
{
     use HasFactory;

     protected $fillable = [
          'name',
          'address',
          'latitude',
          'longitude',
          'radius',
          'is_active',
          'description',
     ];

     protected function casts(): array
     {
          return [
               'latitude' => 'decimal:8',
               'longitude' => 'decimal:8',
               'is_active' => 'boolean',
          ];
     }

     // Relationships
     public function attendances()
     {
          return $this->hasMany(Attendance::class);
     }

     // Helper methods
     public function getDistanceFrom($latitude, $longitude)
     {
          $earthRadius = 6371000; // Earth's radius in meters

          $latFrom = deg2rad($this->latitude);
          $lonFrom = deg2rad($this->longitude);
          $latTo = deg2rad($latitude);
          $lonTo = deg2rad($longitude);

          $latDelta = $latTo - $latFrom;
          $lonDelta = $lonTo - $lonFrom;

          $a = sin($latDelta / 2) * sin($latDelta / 2) +
               cos($latFrom) * cos($latTo) *
               sin($lonDelta / 2) * sin($lonDelta / 2);
          $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

          return $earthRadius * $c; // Distance in meters
     }

     public function isWithinRadius($latitude, $longitude)
     {
          $distance = $this->getDistanceFrom($latitude, $longitude);
          return $distance <= $this->radius;
     }

     // Scope for active locations
     public function scopeActive($query)
     {
          return $query->where('is_active', true);
     }

     // Get nearest active location to given coordinates
     public static function getNearestLocation($latitude, $longitude)
     {
          $locations = static::active()->get();
          $nearestLocation = null;
          $shortestDistance = PHP_FLOAT_MAX;

          foreach ($locations as $location) {
               $distance = $location->getDistanceFrom($latitude, $longitude);
               if ($distance < $shortestDistance) {
                    $shortestDistance = $distance;
                    $nearestLocation = $location;
               }
          }

          return $nearestLocation;
     }

     // Check if coordinates are within any active location
     public static function getValidLocationFor($latitude, $longitude)
     {
          $locations = static::active()->get();

          foreach ($locations as $location) {
               if ($location->isWithinRadius($latitude, $longitude)) {
                    return $location;
               }
          }

          return null;
     }
}
