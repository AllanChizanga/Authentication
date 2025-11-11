<?php

namespace Tests\Feature;

use App\Models\Otp;
use Tests\TestCase;
use App\Models\User;
use App\Models\OtpVerification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OtpAuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_initiate_registration_with_phone_number()
    {
        $response = $this->postJson('/api/auth/register/initiate', [
            'phone_number' => '+1234567890',
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'session_token',
                    'expires_in',
                    'message',
                    'purpose'
                ])
                ->assertJson([
                    'purpose' => 'registration',
                    'message' => 'OTP sent successfully'
                ]);

        $this->assertDatabaseHas('otp_verifications', [
            'phone_number' => '+1234567890',
            'purpose' => 'registration'
        ]);
    }

    /** @test */
    public function user_cannot_initiate_registration_with_existing_phone_number()
    {
        // Create a user with the phone number first
        User::factory()->create(['phone_number' => '+1234567890']);

        $response = $this->postJson('/api/auth/register/initiate', [
            'phone_number' => '+1234567890',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['phone_number']);
    }

    /** @test */
    public function user_can_verify_registration_otp()
    {
        // Create an OTP verification record
        $otp = Otp::factory()->create([
            'phone_number' => '+1234567890',
            'purpose' => 'registration',
            'otp_code' => '123456',
            'session_token' => 'test-session-token',
        ]);

        $response = $this->postJson('/api/auth/register/verify-otp', [
            'phone_number' => '+1234567890',
            'otp_code' => '123456',
            'session_token' => 'test-session-token',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'OTP verified successfully. You can now complete registration.'
                ]);

        // Check that OTP was marked as verified
        $this->assertNotNull($otp->fresh()->verified_at);
    }

    /** @test */
    public function user_cannot_verify_registration_with_invalid_otp()
    {
       Otp::factory()->create([
            'phone_number' => '+1234567890',
            'purpose' => 'registration',
            'otp_code' => '123456',
            'session_token' => 'test-session-token',
        ]);

        $response = $this->postJson('/api/auth/register/verify-otp', [
            'phone_number' => '+1234567890',
            'otp_code' => 'wrong-otp',
            'session_token' => 'test-session-token',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['otp_code']);
    }

    /** @test */
    public function user_can_complete_registration_after_otp_verification()
    {
        // Create a verified OTP session
        $otp =Otp::factory()->create([
            'phone_number' => '+1234567890',
            'purpose' => 'registration',
            'session_token' => 'test-session-token',
            'verified_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/register/complete', [
            'session_token' => 'test-session-token',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'user' => ['id', 'name', 'email', 'phone_number', 'phone_verified_at'],
                    'token',
                    'token_type',
                    'expires_at'
                ])
                ->assertJson([
                    'message' => 'Registration completed successfully',
                    'user' => [
                        'name' => 'John Doe',
                        'email' => 'john@example.com',
                        'phone_number' => '+1234567890',
                    ]
                ]);

        // Check user was created with verified phone
        $this->assertDatabaseHas('users', [
            'phone_number' => '+1234567890',
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);

        $user = User::where('phone_number', '+1234567890')->first();
        $this->assertNotNull($user->phone_verified_at);
    }

    /** @test */
    public function user_can_initiate_login_with_phone_number()
    {
        $user = User::factory()->create(['phone_number' => '+1234567890']);

        $response = $this->postJson('/api/auth/login/initiate', [
            'phone_number' => '+1234567890',
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'session_token',
                    'expires_in',
                    'message',
                    'purpose'
                ])
                ->assertJson([
                    'purpose' => 'login',
                    'message' => 'OTP sent successfully'
                ]);

        $this->assertDatabaseHas('otp_verifications', [
            'phone_number' => '+1234567890',
            'purpose' => 'login',
            'user_id' => $user->id
        ]);
    }

    /** @test */
    public function user_cannot_initiate_login_with_nonexistent_phone_number()
    {
        $response = $this->postJson('/api/auth/login/initiate', [
            'phone_number' => '+1987654321',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['phone_number']);
    }

    /** @test */
    public function user_can_verify_login_otp()
    {
        $user = User::factory()->create(['phone_number' => '+1234567890']);
        
        $otp =Otp::factory()->create([
            'phone_number' => '+1234567890',
            'purpose' => 'login',
            'otp_code' => '123456',
            'session_token' => 'test-session-token',
            'user_id' => $user->id,
        ]);

        $response = $this->postJson('/api/auth/login/verify-otp', [
            'phone_number' => '+1234567890',
            'otp_code' => '123456',
            'session_token' => 'test-session-token',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'OTP verified successfully. You can now complete login.'
                ]);

        $this->assertNotNull($otp->fresh()->verified_at);
    }

    /** @test */
    public function user_can_complete_login_after_otp_verification()
    {
        $user = User::factory()->create([
            'phone_number' => '+1234567890',
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $otp =Otp::factory()->create([
            'phone_number' => '+1234567890',
            'purpose' => 'login',
            'session_token' => 'test-session-token',
            'verified_at' => now(),
            'user_id' => $user->id,
        ]);

        $response = $this->postJson('/api/auth/login/complete', [
            'session_token' => 'test-session-token',
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'user' => ['id', 'name', 'email', 'phone_number'],
                    'token',
                    'token_type',
                    'expires_at'
                ])
                ->assertJson([
                    'message' => 'Login successful',
                    'user' => [
                        'name' => 'John Doe',
                        'email' => 'john@example.com',
                        'phone_number' => '+1234567890',
                    ]
                ]);
    }

    /** @test */
    public function rate_limiting_prevents_too_many_otp_requests()
    {
        // Mock the rate limit to be at maximum
        Cache::put('otp_rate_limit:+1234567890:registration', 5, now()->addHour());

        $response = $this->postJson('/api/auth/register/initiate', [
            'phone_number' => '+1234567890',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['phone_number']);
    }

    /** @test */
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
                ->assertJson(['message' => 'Successfully logged out']);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id
        ]);
    }

    /** @test */
    public function authenticated_user_can_get_their_profile()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/auth/user');

        $response->assertStatus(200)
                ->assertJson([
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone_number' => $user->phone_number,
                    ]
                ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_protected_routes()
    {
        $response = $this->getJson('/api/auth/user');

        $response->assertStatus(401);
    }
}