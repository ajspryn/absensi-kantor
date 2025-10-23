<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppSetting extends Model
{
     use HasFactory;

     protected $fillable = [
          'key',
          'value',
          'type',
          'group',
          'label',
          'description',
          'is_public',
     ];

     protected function casts(): array
     {
          return [
               'is_public' => 'boolean',
          ];
     }

     /**
      * Get setting value with proper type casting
      */
     public function getValue()
     {
          switch ($this->type) {
               case 'boolean':
                    return (bool) $this->value;
               case 'number':
                    return is_numeric($this->value) ? (float) $this->value : 0;
               case 'json':
                    return json_decode($this->value, true);
               default:
                    return $this->value;
          }
     }

     /**
      * Set setting value with proper type conversion
      */
     public function setValue($value)
     {
          switch ($this->type) {
               case 'boolean':
                    $this->value = $value ? '1' : '0';
                    break;
               case 'json':
                    $this->value = json_encode($value);
                    break;
               default:
                    $this->value = (string) $value;
          }
     }

     /**
      * Get setting by key
      */
     public static function getSetting($key, $default = null)
     {
          $setting = static::where('key', $key)->first();
          return $setting ? $setting->getValue() : $default;
     }

     /**
      * Set setting by key
      */
     public static function setSetting($key, $value)
     {
          $setting = static::where('key', $key)->first();
          if ($setting) {
               $setting->setValue($value);
               $setting->save();
          }
          return $setting;
     }

     /**
      * Get settings by group
      */
     public static function getByGroup($group)
     {
          return static::where('group', $group)->get()->keyBy('key');
     }

     /**
      * Get application logo URL
      */
     public static function getLogoUrl($default = null)
     {
          $logoPath = static::getSetting('app_logo');
              if ($logoPath && file_exists(public_path('storage/' . $logoPath))) {
                    $fullPath = public_path('storage/' . $logoPath);
                    $mtime = filemtime($fullPath);
                    return asset('storage/' . $logoPath) . '?v=' . $mtime;
               }
          return $default ?: asset('template/images/logos/logo.png');
     }

     /**
      * Get application favicon URL
      */
     public static function getFaviconUrl($default = null)
     {
          $faviconPath = static::getSetting('app_favicon');
              if ($faviconPath && file_exists(public_path('storage/' . $faviconPath))) {
                    $fullPath = public_path('storage/' . $faviconPath);
                    $mtime = filemtime($fullPath);
                    return asset('storage/' . $faviconPath) . '?v=' . $mtime;
               }
          return $default ?: asset('template/images/favicons/favicon.ico');
     }
}
