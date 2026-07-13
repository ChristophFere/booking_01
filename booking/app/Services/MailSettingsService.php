<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;

class MailSettingsService
{
    /** @var list<string> */
    private const KEYS = [
        'mail.mailer',
        'mail.host',
        'mail.port',
        'mail.username',
        'mail.password',
        'mail.encryption',
        'mail.from_address',
        'mail.from_name',
    ];

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return [
            'mailer' => Setting::get('mail.mailer', config('mail.default')),
            'host' => Setting::get('mail.host', config('mail.mailers.smtp.host')),
            'port' => Setting::get('mail.port', config('mail.mailers.smtp.port')),
            'username' => Setting::get('mail.username', config('mail.mailers.smtp.username')),
            'password' => Setting::get('mail.password', config('mail.mailers.smtp.password')),
            'encryption' => Setting::get('mail.encryption', config('mail.mailers.smtp.encryption')),
            'from_address' => Setting::get('mail.from_address', config('mail.from.address')),
            'from_name' => Setting::get('mail.from_name', config('mail.from.name')),
            'has_stored_password' => Setting::has('mail.password'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function save(array $data): void
    {
        Setting::set('mail.mailer', $data['mailer']);
        Setting::set('mail.host', $data['host']);
        Setting::set('mail.port', (string) $data['port']);
        Setting::set('mail.username', $data['username'] ?? '');
        Setting::set('mail.encryption', $data['encryption'] ?? '');
        Setting::set('mail.from_address', $data['from_address']);
        Setting::set('mail.from_name', $data['from_name']);

        if (! empty($data['password'])) {
            Setting::set('mail.password', $data['password']);
        }

        $this->applyToConfig();
    }

    public function applyToConfig(): void
    {
        if (! Setting::has('mail.host')) {
            return;
        }

        $settings = $this->all();

        Config::set('mail.default', $settings['mailer'] ?: 'smtp');
        Config::set('mail.mailers.smtp.host', $settings['host']);
        Config::set('mail.mailers.smtp.port', (int) $settings['port']);
        Config::set('mail.mailers.smtp.username', $settings['username']);
        Config::set('mail.mailers.smtp.password', $settings['password']);
        Config::set('mail.mailers.smtp.encryption', $settings['encryption'] ?: null);
        Config::set('mail.from.address', $settings['from_address']);
        Config::set('mail.from.name', $settings['from_name']);
    }
}
