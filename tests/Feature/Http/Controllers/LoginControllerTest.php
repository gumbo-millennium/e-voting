<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use App\Services\VerificationService;
use Illuminate\Foundation\Testing\WithFaker;
use OTPHP\TOTPInterface;
use Tests\Fixtures\Services\Verification\TestingService;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use WithFaker;

    /**
     * Replace the VerificationService handlers with a test
     * service.
     *
     * @before
     * @return void
     */
    public function bindAfterSetup(): void
    {
        $this->afterApplicationCreated(function () {
            $this->app->singleton(TestingService::class);

            $this->app->instance(
                VerificationService::class,
                new VerificationService([
                    $this->app->make(TestingService::class),
                ])
            );
        });
    }

    public function testLoginIndex(): void
    {
        $this->get('/login')->assertOk();
    }

    public function testInvalidUser(): void
    {
        $this->post('/login', [
            'email' => 'missing@example.com',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('message', 'Deze gebruiker kon niet worden gevonden.');

        $this->app->make(TestingService::class)->assertNothingSent();
    }

    public function testValidUserSansNumber(): void
    {
        User::factory()->create([
            'email' => 'no-phone@example.com',
            'phone' => null,
        ]);

        $this->post('/login', [
            'email' => 'no-phone@example.com',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas(
                'message',
                'Van deze gebruiker is geen telefoonnummer bekend, je kan dus niet inloggen.'
            );

        $this->app->make(TestingService::class)->assertNothingSent();
    }

    public function testValidUser(): void
    {
        $user = User::factory()->create([
            'email' => 'valid@example.com',
            'phone' => $this->faker->numerify('+31642######'),
        ]);
        \assert($user instanceof User);

        $this->post('/login', [
            'email' => 'valid@example.com',
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login.verify'));

        $totpToken = $user->refresh()->totp;
        \assert($totpToken instanceof TOTPInterface);

        $currentToken = $totpToken->now();
        $splitToken = implode(' ', str_split($currentToken, 4));

        $this->app->make(TestingService::class)->assertSent($user->phone, <<<SMS
        Je code om in te loggen voor e-voting is {$splitToken}.

        @localhost #{$currentToken}
        SMS);

        $this->post('/login/verify', [
            'token' => $splitToken,
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }
}
