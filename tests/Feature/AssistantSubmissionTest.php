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

        $this->post('/assistant/submit', $this->validPayload())
            ->assertRedirect();

        $this->assertDatabaseHas('assistant_requests', [
            'email' => 'jane@example.com',
            'name' => 'Jane Doe',
        ]);

        Mail::assertSent(AssistantSubmissionReceived::class);
    }

    public function test_assistant_submission_returns_json_for_ajax(): void
    {
        Mail::fake();

        $this->postJson('/assistant/submit', $this->validPayload())
            ->assertOk()
            ->assertJson([
                'message' => 'Your message has been sent. We will contact you shortly.',
            ]);

        $this->assertDatabaseHas('assistant_requests', [
            'email' => 'jane@example.com',
        ]);
    }

    public function test_assistant_submission_rejects_honeypot_spam(): void
    {
        $this->from('/contact-us')
            ->post('/assistant/submit', $this->validPayload([
                'name' => 'Bot',
                'email' => 'bot@example.com',
                'website' => 'https://spam.example',
            ]))
            ->assertSessionHasErrors('website');

        $this->assertDatabaseCount('assistant_requests', 0);
    }

    public function test_assistant_submission_rejects_second_honeypot(): void
    {
        $this->from('/contact-us')
            ->post('/assistant/submit', $this->validPayload([
                'company' => 'Spam Co',
            ]))
            ->assertSessionHasErrors('company');

        $this->assertDatabaseCount('assistant_requests', 0);
    }

    public function test_assistant_submission_rejects_too_many_links(): void
    {
        $this->from('/contact-us')
            ->post('/assistant/submit', $this->validPayload([
                'message' => 'See http://a.com http://b.com http://c.com http://d.com for more.',
            ]))
            ->assertSessionHasErrors('message');

        $this->assertDatabaseCount('assistant_requests', 0);
    }

    public function test_assistant_submission_rejects_invalid_form_token(): void
    {
        $this->from('/contact-us')
            ->post('/assistant/submit', $this->validPayload([
                'form_token' => 'not-a-token',
            ]))
            ->assertSessionHasErrors('form_token');

        $this->assertDatabaseCount('assistant_requests', 0);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '0712345678',
            'message' => 'I need pricing for bulk graduation gown hire.',
            'website' => '',
            'company' => '',
            'form_token' => encrypt(['t' => now()->subSeconds(5)->timestamp]),
        ], $overrides);
    }
}
