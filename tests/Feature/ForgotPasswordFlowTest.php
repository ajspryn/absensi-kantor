<?php

namespace Tests\Feature;

use App\Models\PasswordResetRequest;
use App\Models\Role;
use App\Models\User;
use App\Notifications\PasswordResetApproved;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_request_reset_then_admin_approves_then_user_resets_password(): void
    {
        Notification::fake();

        $adminRole = Role::factory()->create(['name' => 'admin', 'permissions' => []]);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        $userRole = Role::factory()->create(['name' => 'employee', 'permissions' => []]);
        $user = User::factory()->create(['role_id' => $userRole->id]);

        $this->assertTrue(Hash::check('password', $user->password));

        // 1) Guest submits forgot-password request
        $this->post(route('password.request'), [
            'email' => $user->email,
            'reason' => 'Lupa password setelah ganti HP',
        ])->assertRedirect(route('login'));

        $resetRequest = PasswordResetRequest::query()->where('email', $user->email)->firstOrFail();
        $this->assertSame('pending', $resetRequest->status);
        $this->assertNotEmpty($resetRequest->token);

        // Duplicate request while pending should be rejected
        $this->from(route('password.request.form'))
            ->post(route('password.request'), [
                'email' => $user->email,
                'reason' => 'Coba lagi',
            ])
            ->assertRedirect(route('password.request.form'));

        // 2) Admin approves
        $this->actingAs($admin)
            ->patch(route('admin.password-reset.approve', $resetRequest->id))
            ->assertRedirect();

        // Route reset-password berada di group guest; pastikan kembali jadi guest.
        Auth::logout();

        $resetRequest->refresh();
        $this->assertSame('approved', $resetRequest->status);

        Notification::assertSentTo($user, PasswordResetApproved::class);

        // 3) Guest opens reset form and submits new password
        $this->get(route('password.reset', $resetRequest->token))
            ->assertOk()
            ->assertSee($user->email);

        $this->post(route('password.update'), [
            'token' => $resetRequest->token,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect(route('login'));

        $resetRequest->refresh();
        $this->assertSame('used', $resetRequest->status);

        $user->refresh();
        $this->assertTrue(Hash::check('new-password-123', $user->password));
    }
}
