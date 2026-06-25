<?php

namespace App\Services\Print;

use App\Models\PrintTemplate;
use App\Models\PrintTemplateVersion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PrintTemplateService
{
    public function store(array $data): PrintTemplate
    {
        return DB::transaction(function () use ($data) {
            $template = PrintTemplate::create([
                'branch_id' => $data['branch_id'] ?? auth()->user()?->branch_id ?? 1,
                'name' => $data['name'],
                'slug' => $data['slug'] ?? Str::slug($data['name']),
                'document_type' => $data['document_type'],
                'paper_size_id' => $data['paper_size_id'] ?? null,
                'printer_profile_id' => $data['printer_profile_id'] ?? null,
                'status' => PrintTemplate::STATUS_DRAFT,
                'version' => 1,
                'layout' => $data['layout'] ?? ['components' => []],
                'header' => $data['header'] ?? $this->defaultHeader(),
                'footer' => $data['footer'] ?? $this->defaultFooter(),
                'theme' => $data['theme'] ?? $this->defaultTheme(),
                'settings' => $data['settings'] ?? [],
                'is_default' => $data['is_default'] ?? false,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            $this->saveVersion($template, 'Initial version');

            return $template;
        });
    }

    public function update(PrintTemplate $template, array $data, bool $newVersion = false): PrintTemplate
    {
        return DB::transaction(function () use ($template, $data, $newVersion) {
            if ($newVersion) {
                $template->version++;
            }

            $template->update(array_filter([
                'name' => $data['name'] ?? null,
                'slug' => $data['slug'] ?? null,
                'document_type' => $data['document_type'] ?? null,
                'paper_size_id' => $data['paper_size_id'] ?? null,
                'printer_profile_id' => $data['printer_profile_id'] ?? null,
                'layout' => $data['layout'] ?? null,
                'header' => $data['header'] ?? null,
                'footer' => $data['footer'] ?? null,
                'theme' => $data['theme'] ?? null,
                'settings' => $data['settings'] ?? null,
                'version' => $template->version,
                'updated_by' => auth()->id(),
            ], fn ($v) => $v !== null));

            if ($newVersion) {
                $this->saveVersion($template, $data['version_note'] ?? 'Updated');
            }

            return $template->fresh();
        });
    }

    public function publish(PrintTemplate $template): PrintTemplate
    {
        if ($template->is_default) {
            PrintTemplate::where('document_type', $template->document_type)
                ->where('id', '!=', $template->id)
                ->update(['is_default' => false]);
        }

        $template->update([
            'status' => PrintTemplate::STATUS_PUBLISHED,
            'published_at' => now(),
            'updated_by' => auth()->id(),
        ]);

        return $template;
    }

    public function duplicate(PrintTemplate $template): PrintTemplate
    {
        $copy = $template->replicate(['slug', 'status', 'published_at', 'is_default']);
        $copy->name = $template->name.' (Copy)';
        $copy->slug = $template->slug.'-copy-'.Str::random(4);
        $copy->status = PrintTemplate::STATUS_DRAFT;
        $copy->version = 1;
        $copy->is_default = false;
        $copy->created_by = auth()->id();
        $copy->save();

        $this->saveVersion($copy, 'Duplicated from #'.$template->id);

        return $copy;
    }

    public function restoreVersion(PrintTemplate $template, int $versionId): PrintTemplate
    {
        $ver = PrintTemplateVersion::where('print_template_id', $template->id)->findOrFail($versionId);

        return $this->update($template, [
            'layout' => $ver->layout,
            'header' => $ver->header,
            'footer' => $ver->footer,
            'theme' => $ver->theme,
            'settings' => $ver->settings,
            'version_note' => 'Restored v'.$ver->version,
        ], true);
    }

    public function export(PrintTemplate $template): array
    {
        return [
            'name' => $template->name,
            'document_type' => $template->document_type,
            'layout' => $template->layout,
            'header' => $template->header,
            'footer' => $template->footer,
            'theme' => $template->theme,
            'settings' => $template->settings,
            'exported_at' => now()->toIso8601String(),
        ];
    }

    public function import(array $payload): PrintTemplate
    {
        return $this->store([
            'name' => ($payload['name'] ?? 'Imported').' '.now()->format('His'),
            'document_type' => $payload['document_type'] ?? 'custom',
            'layout' => $payload['layout'] ?? ['components' => []],
            'header' => $payload['header'] ?? $this->defaultHeader(),
            'footer' => $payload['footer'] ?? $this->defaultFooter(),
            'theme' => $payload['theme'] ?? $this->defaultTheme(),
            'settings' => $payload['settings'] ?? [],
        ]);
    }

    protected function saveVersion(PrintTemplate $template, ?string $note = null): void
    {
        PrintTemplateVersion::create([
            'print_template_id' => $template->id,
            'version' => $template->version,
            'layout' => $template->layout,
            'header' => $template->header,
            'footer' => $template->footer,
            'theme' => $template->theme,
            'settings' => $template->settings,
            'note' => $note,
            'created_by' => auth()->id(),
        ]);
    }

    public function defaultHeader(): array
    {
        return [
            'enabled' => true,
            'logo' => false,
            'hospital_name' => true,
            'hospital_address' => true,
            'phone' => false,
            'email' => false,
            'date' => false,
            'align' => 'center',
        ];
    }

    public function defaultFooter(): array
    {
        return [
            'enabled' => true,
            'thank_you' => true,
            'thank_you_text' => 'Thank you for visiting us.',
            'footer_text' => true,
            'terms' => false,
            'align' => 'center',
        ];
    }

    public function defaultTheme(): array
    {
        return [
            'primary' => '#0ea5e9',
            'secondary' => '#06b6d4',
            'text' => '#0f172a',
            'background' => '#ffffff',
            'font' => 'Inter, Arial, sans-serif',
        ];
    }
}
