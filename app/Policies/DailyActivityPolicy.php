<?php

namespace App\Policies;

use App\Models\DailyActivity;
use App\Models\User;

class DailyActivityPolicy
{
     /**
      * Determine whether the user can view the activity.
      */
     public function view(User $user, DailyActivity $activity): bool
     {
          // Owner can view
          if ($activity->employee_id === ($user->employee?->id ?? null)) {
               return true;
          }

          // Managers with department permission can view activities in their department
          if ($user->role && $user->role->hasPermission('daily_activities.view_department')) {
               $userDept = $user->employee?->department_id ?? null;
               return $userDept && $activity->employee && $activity->employee->department_id === $userDept;
          }

          return false;
     }

     /**
      * Determine whether the user can create activities.
      */
     public function create(User $user): bool
     {
          return $user->role && $user->role->hasPermission('daily_activities.create');
     }
}
