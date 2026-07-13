<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SendTestMailRequest;
use App\Http\Requests\Admin\UpdateMailSettingsRequest;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use App\Mail\TestMail;
use App\Services\MailSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class SettingsController extends AdminController
{
    public function __construct(
        private MailSettingsService $mailSettingsService,
    ) {}

    public function index(): View
    {
        return view('admin.settings.index', [
            'mailSettings' => collect($this->mailSettingsService->all())
                ->except('password')
                ->all(),
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Passwort wurde erfolgreich geändert.');
    }

    public function updateMail(UpdateMailSettingsRequest $request): RedirectResponse
    {
        $this->mailSettingsService->save($request->validated());

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'E-Mail-Einstellungen wurden gespeichert.');
    }

    public function sendTestMail(SendTestMailRequest $request): RedirectResponse
    {
        $this->mailSettingsService->applyToConfig();

        $recipient = $request->validated('recipient');
        $mailer = config('mail.default');

        try {
            Mail::to($recipient)->send(new TestMail($request->user()->name));
        } catch (Throwable $exception) {
            return redirect()
                ->route('admin.settings.index')
                ->withInput()
                ->with('error', 'Test-E-Mail konnte nicht gesendet werden: '.$exception->getMessage());
        }

        $message = $mailer === 'log'
            ? "Test-E-Mail wurde an den Log geschrieben (Versandart: Log). Prüfen Sie storage/logs/laravel.log."
            : "Test-E-Mail wurde an {$recipient} gesendet.";

        return redirect()
            ->route('admin.settings.index')
            ->with('success', $message);
    }
}
