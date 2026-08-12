<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Setting;

class EmailTemplateSettingsService
{
    private const KEY_CONFIRMATION_SUBJECT = 'mail.template.confirmation.subject';

    private const KEY_CONFIRMATION_BODY = 'mail.template.confirmation.body';

    private const KEY_REJECTION_PENDING_SUBJECT = 'mail.template.rejection_pending.subject';

    private const KEY_REJECTION_PENDING_BODY = 'mail.template.rejection_pending.body';

    private const KEY_REJECTION_CANCELLED_SUBJECT = 'mail.template.rejection_cancelled.subject';

    private const KEY_REJECTION_CANCELLED_BODY = 'mail.template.rejection_cancelled.body';

    /**
     * @return array<int, string>
     */
    public function placeholders(): array
    {
        return [
            '{{customer_name}}',
            '{{service_name}}',
            '{{date}}',
            '{{start_time}}',
            '{{end_time}}',
            '{{admin_notes}}',
        ];
    }

    /**
     * @return array{
     *     confirmation_subject: string,
     *     confirmation_body: string,
     *     rejection_pending_subject: string,
     *     rejection_pending_body: string,
     *     rejection_cancelled_subject: string,
     *     rejection_cancelled_body: string
     * }
     */
    public function all(): array
    {
        return [
            'confirmation_subject' => $this->getConfirmationSubject(),
            'confirmation_body' => $this->getConfirmationBody(),
            'rejection_pending_subject' => $this->getRejectionPendingSubject(),
            'rejection_pending_body' => $this->getRejectionPendingBody(),
            'rejection_cancelled_subject' => $this->getRejectionCancelledSubject(),
            'rejection_cancelled_body' => $this->getRejectionCancelledBody(),
        ];
    }

    /**
     * @param  array{
     *     confirmation_subject: string,
     *     confirmation_body: string,
     *     rejection_pending_subject: string,
     *     rejection_pending_body: string,
     *     rejection_cancelled_subject: string,
     *     rejection_cancelled_body: string
     * }  $data
     */
    public function save(array $data): void
    {
        Setting::set(self::KEY_CONFIRMATION_SUBJECT, $data['confirmation_subject']);
        Setting::set(self::KEY_CONFIRMATION_BODY, $data['confirmation_body']);
        Setting::set(self::KEY_REJECTION_PENDING_SUBJECT, $data['rejection_pending_subject']);
        Setting::set(self::KEY_REJECTION_PENDING_BODY, $data['rejection_pending_body']);
        Setting::set(self::KEY_REJECTION_CANCELLED_SUBJECT, $data['rejection_cancelled_subject']);
        Setting::set(self::KEY_REJECTION_CANCELLED_BODY, $data['rejection_cancelled_body']);
    }

    /**
     * @return array{subject: string, body: string}
     */
    public function renderConfirmation(Appointment $appointment): array
    {
        $replacements = $this->replacementsFor($appointment);

        return [
            'subject' => $this->replacePlaceholders($this->getConfirmationSubject(), $replacements),
            'body' => $this->replacePlaceholders($this->getConfirmationBody(), $replacements),
        ];
    }

    /**
     * @return array{subject: string, body: string}
     */
    public function renderRejection(Appointment $appointment): array
    {
        $replacements = $this->replacementsFor($appointment);

        if ($appointment->confirmed_at) {
            return [
                'subject' => $this->replacePlaceholders($this->getRejectionCancelledSubject(), $replacements),
                'body' => $this->replacePlaceholders($this->getRejectionCancelledBody(), $replacements),
            ];
        }

        return [
            'subject' => $this->replacePlaceholders($this->getRejectionPendingSubject(), $replacements),
            'body' => $this->replacePlaceholders($this->getRejectionPendingBody(), $replacements),
        ];
    }

    public function getConfirmationSubject(): string
    {
        return Setting::get(self::KEY_CONFIRMATION_SUBJECT, 'Terminbestätigung');
    }

    public function getConfirmationBody(): string
    {
        return Setting::get(self::KEY_CONFIRMATION_BODY, $this->defaultConfirmationBody());
    }

    public function getRejectionPendingSubject(): string
    {
        return Setting::get(self::KEY_REJECTION_PENDING_SUBJECT, 'Ihre Terminanfrage');
    }

    public function getRejectionPendingBody(): string
    {
        return Setting::get(self::KEY_REJECTION_PENDING_BODY, $this->defaultRejectionPendingBody());
    }

    public function getRejectionCancelledSubject(): string
    {
        return Setting::get(self::KEY_REJECTION_CANCELLED_SUBJECT, 'Terminstornierung');
    }

    public function getRejectionCancelledBody(): string
    {
        return Setting::get(self::KEY_REJECTION_CANCELLED_BODY, $this->defaultRejectionCancelledBody());
    }

    /**
     * @return array<string, string>
     */
    private function replacementsFor(Appointment $appointment): array
    {
        $appointment->loadMissing('service');

        return [
            '{{customer_name}}' => $appointment->customer_name,
            '{{service_name}}' => $appointment->service->name,
            '{{date}}' => $appointment->starts_at->format('d.m.Y'),
            '{{start_time}}' => $appointment->starts_at->format('H:i'),
            '{{end_time}}' => $appointment->ends_at->format('H:i'),
            '{{admin_notes}}' => $appointment->admin_notes ?? '',
        ];
    }

    /**
     * @param  array<string, string>  $replacements
     */
    private function replacePlaceholders(string $template, array $replacements): string
    {
        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    private function defaultConfirmationBody(): string
    {
        return <<<'HTML'
<p>Hallo {{customer_name}},</p>

<p>wir freuen uns, Ihnen mitteilen zu können, dass Ihr Termin bestätigt wurde.</p>

<p><strong>Ihre Termindetails:</strong></p>
<ul>
    <li><strong>Leistung:</strong> {{service_name}}</li>
    <li><strong>Datum:</strong> {{date}}</li>
    <li><strong>Uhrzeit:</strong> {{start_time}} – {{end_time}} Uhr</li>
</ul>

<p>Bitte erscheinen Sie pünktlich zu Ihrem Termin. Bei Fragen können Sie uns gerne kontaktieren.</p>

<p>Mit freundlichen Grüßen<br>Ihr Team</p>
HTML;
    }

    private function defaultRejectionPendingBody(): string
    {
        return <<<'HTML'
<p>Hallo {{customer_name}},</p>

<p>vielen Dank für Ihre Terminanfrage. Leider können wir Ihnen den gewünschten Termin am {{date}} um {{start_time}} Uhr ({{service_name}}) nicht bestätigen.</p>

<p><strong>Begründung:</strong></p>
<p>{{admin_notes}}</p>

<p>Wir bedauern die Unannehmlichkeiten und hoffen auf Ihr Verständnis.</p>

<p>Mit freundlichen Grüßen<br>Ihr Team</p>
HTML;
    }

    private function defaultRejectionCancelledBody(): string
    {
        return <<<'HTML'
<p>Hallo {{customer_name}},</p>

<p>leider müssen wir Ihnen mitteilen, dass Ihr bestätigter Termin am {{date}} um {{start_time}} Uhr ({{service_name}}) storniert wurde.</p>

<p><strong>Begründung:</strong></p>
<p>{{admin_notes}}</p>

<p>Wir bedauern die Unannehmlichkeiten und hoffen auf Ihr Verständnis.</p>

<p>Mit freundlichen Grüßen<br>Ihr Team</p>
HTML;
    }
}
