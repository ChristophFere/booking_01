<?php

namespace Tests\Unit;

use App\Models\Appointment;
use App\Models\Service;
use App\Services\EmailTemplateSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTemplateSettingsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_confirmation_with_placeholders(): void
    {
        $service = new Service(['name' => 'Tischtennis', 'duration_minutes' => 120, 'is_active' => true]);
        $service->save();

        $appointment = Appointment::factory()->for($service)->create([
            'customer_name' => 'TSV Beispiel / Max Mustermann',
            'admin_notes' => 'Testgrund',
        ]);

        $rendered = app(EmailTemplateSettingsService::class)->renderConfirmation($appointment);

        $this->assertSame('Terminbestätigung', $rendered['subject']);
        $this->assertStringContainsString('TSV Beispiel / Max Mustermann', $rendered['body']);
        $this->assertStringContainsString('Tischtennis', $rendered['body']);
        $this->assertStringContainsString($appointment->starts_at->format('d.m.Y'), $rendered['body']);
        $this->assertStringNotContainsString('{{customer_name}}', $rendered['body']);
    }

    public function test_renders_rejection_for_pending_and_cancelled_appointments(): void
    {
        $service = app(EmailTemplateSettingsService::class);

        $pending = Appointment::factory()->for(Service::factory()->create())->create([
            'confirmed_at' => null,
            'admin_notes' => 'Kein Platz frei',
        ]);

        $cancelled = Appointment::factory()->for(Service::factory()->create())->confirmed()->create([
            'admin_notes' => 'Wetter',
        ]);

        $pendingRendered = $service->renderRejection($pending);
        $cancelledRendered = $service->renderRejection($cancelled);

        $this->assertSame('Ihre Terminanfrage', $pendingRendered['subject']);
        $this->assertStringContainsString('Kein Platz frei', $pendingRendered['body']);

        $this->assertSame('Terminstornierung', $cancelledRendered['subject']);
        $this->assertStringContainsString('storniert wurde', $cancelledRendered['body']);
    }

    public function test_saves_custom_templates(): void
    {
        $service = app(EmailTemplateSettingsService::class);

        $service->save([
            'confirmation_subject' => 'Ihr Termin steht',
            'confirmation_body' => '<p>Hallo {{customer_name}}</p>',
            'rejection_pending_subject' => 'Absage',
            'rejection_pending_body' => '<p>Leider nein</p>',
            'rejection_cancelled_subject' => 'Storno',
            'rejection_cancelled_body' => '<p>Storniert</p>',
        ]);

        $this->assertSame('Ihr Termin steht', $service->getConfirmationSubject());
        $this->assertSame('<p>Hallo {{customer_name}}</p>', $service->getConfirmationBody());
    }
}
