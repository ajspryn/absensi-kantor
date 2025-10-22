<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\PasswordResetRequest;
use App\Models\Role;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PasswordResetApproved;

class PasswordResetApprovalTest extends TestCase
{
     use RefreshDatabase;

     public function test_admin_can_approve_and_user_gets_notified()
     {
          Notification::fake();

          // Create roles and users
          $adminRole = Role::factory()->create(['name' => 'admin', 'permissions' => []]);
          $admin = User::factory()->create(['role_id' => $adminRole->id]);

          $userRole = Role::factory()->create(['name' => 'employee', 'permissions' => []]);
          $user = User::factory()->create(['role_id' => $userRole->id]);

          // Create a password reset request for the user
          $request = PasswordResetRequest::create([
               'email' => $user->email,
               'token' => 'test-token-123',
               'status' => 'pending',
               'reason' => 'Forgot password',
               'expires_at' => now()->addDay(),
          ]);

          // Acting as admin approve the request via controller route
          $this->actingAs($admin)
               ->patch(route('admin.password-reset.approve', $request->id))
               ->assertRedirect();

          $request->refresh();
          $this->assertEquals('approved', $request->status);

          // Assert notification was sent to the user
          Notification::assertSentTo($user, PasswordResetApproved::class);
     }
}
