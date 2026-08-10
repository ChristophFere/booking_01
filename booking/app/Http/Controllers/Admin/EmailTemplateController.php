<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\UpdateEmailTemplateSettingsRequest;
use App\Services\EmailTemplateSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmailTemplateController extends AdminController
{
    public function __construct(
        private EmailTemplateSettingsService $emailTemplates,
    ) {}

    public function index(): View
    {
        return view('admin.email-templates.index', [
            'templates' => $this->emailTemplates->all(),
            'placeholders' => $this->emailTemplates->placeholders(),
        ]);
    }

    public function update(UpdateEmailTemplateSettingsRequest $request): RedirectResponse
    {
        $this->emailTemplates->save($request->validated());

        return redirect()
            ->route('admin.email-templates.index')
            ->with('success', 'E-Mail-Vorlagen wurden gespeichert.');
    }
}
