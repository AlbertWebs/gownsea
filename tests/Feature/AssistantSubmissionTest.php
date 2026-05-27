<?php

namespace Tests\Feature;

use App\Mail\AssistantSubmissionReceived;
use App\Models\AssistantRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AssistantSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_assistant_submission_is_stored_and_email_sent(): void
    {
        Mail::fake();

        $payload = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '0712345678',
            'message' => 'I need pricing for bulk graduation gown hire.',
            'website' => '',
        ];

        $this->post('/assistant/submit', $payload)
            ->assertRedirect();

        $this->assertDatabaseHas('assistant_requests', [
            'email' => 'jane@example.com',
            'name' => 'Jane Doe',
        ]);

        Mail::assertSent(AssistantSubmissionReceived::class);
    }

    public function test_assistant_submission_rejects_honeypot_spam(): void
    {
        $payload = [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'phone' => '0700000000',
            'message' => 'Spam payload long enough to pass message minimum.',
            'website' => 'https://spam.example',
        ];

        $this->from('/contact-us')
            ->post('/assistant/submit', $payload)
            ->assertSessionHasErrors('website');

        $this->assertDatabaseCount('assistant_requests', 0);
    }
}
