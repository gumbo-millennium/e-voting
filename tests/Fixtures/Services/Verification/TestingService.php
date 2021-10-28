<?php

declare(strict_types=1);

namespace Tests\Fixtures\Services\Verification;

use App\Contracts\SendsNotifications;
use Tests\TestCase;

class TestingService implements SendsNotifications
{
    private array $sentMessages = [];

    private array $blocklist = [];

    public function preventSendingTo(string $recipient): void
    {
        $this->blocklist[$recipient] = true;
    }

    public function assertNothingSent(): void
    {
        TestCase::assertEmpty($this->sentMessages);
    }

    public function assertSent(string $recipient, ?string $message = null): void
    {
        TestCase::assertArrayHasKey($recipient, $this->sentMessages);

        if (!$message) {
            return;
        }

        TestCase::assertStringContainsString($message, $this->sentMessages[$recipient]);
    }

    /**
     * @inheritdoc
     */
    public function canSendCode(string $recipient): bool
    {
        return !array_key_exists($recipient, $this->blocklist);
    }

    /**
     * @inheritdoc
     */
    public function sendVerificationCode(string $recipient, string $message): bool
    {
        // Fail if missing params
        if (empty($recipient) || empty($message)) {
            return false;
        }

        // Send message to internal array
        $this->sentMessages[$recipient] = $message;

        // Send OK
        return true;
    }
}
