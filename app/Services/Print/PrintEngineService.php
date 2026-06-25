<?php

namespace App\Services\Print;

use App\Models\PaperSize;
use App\Models\PrintSetting;
use App\Models\PrintTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class PrintEngineService
{
    public function __construct(
        protected PrintVariableResolver $variables,
        protected PrintRenderer $renderer,
    ) {}

    public function resolveTemplate(string $documentType, ?int $branchId = null, ?string $slug = null): ?PrintTemplate
    {
        $branchId = $branchId ?? auth()->user()?->branch_id ?? 1;

        $query = PrintTemplate::with(['paperSize', 'printerProfile'])
            ->where('document_type', $documentType)
            ->where('status', PrintTemplate::STATUS_PUBLISHED)
            ->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });

        if ($slug) {
            $query->where('slug', $slug);
        } else {
            $query->where('is_default', true);
        }

        return $query->orderByDesc('is_default')->orderByDesc('id')->first();
    }

    public function render(
        string $documentType,
        mixed $subject,
        array $options = [],
    ): array {
        $branchId = $options['branch_id'] ?? auth()->user()?->branch_id ?? 1;
        $template = $options['template'] ?? $this->resolveTemplate($documentType, $branchId, $options['slug'] ?? null);
        $vars = $options['variables'] ?? $this->variables->resolve($documentType, $subject, $branchId);

        if (! $template) {
            return [
                'html' => $this->fallbackHtml($documentType, $vars),
                'template' => null,
                'variables' => $vars,
                'paper' => $this->defaultPaper(),
            ];
        }

        $paper = $template->paperSize ?? $this->defaultPaper();
        $html = $this->renderer->render($template, $vars, $paper);

        return compact('html', 'template') + [
            'variables' => $vars,
            'paper' => $paper,
        ];
    }

    public function preview(PrintTemplate $template, ?array $vars = null): string
    {
        $vars ??= $this->variables->sampleData();
        $paper = $template->paperSize ?? $this->defaultPaper();

        return $this->renderer->render($template, $vars, $paper);
    }

    public function downloadPdf(string $html, PaperSize $paper, string $filename): \Symfony\Component\HttpFoundation\Response
    {
        $engine = PrintSetting::get('pdf_engine', 'dompdf', auth()->user()?->branch_id ?? 1);

        if ($engine === 'dompdf') {
            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper([$paper->width_mm, $paper->height_mm], $paper->orientation);

            return $pdf->download($filename);
        }

        return Pdf::loadHTML($html)->download($filename);
    }

    protected function defaultPaper(): PaperSize
    {
        return PaperSize::where('slug', 'a4')->first()
            ?? new PaperSize([
                'name' => 'A4', 'slug' => 'a4', 'width_mm' => 210, 'height_mm' => 297,
                'orientation' => 'portrait', 'margins' => ['top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 10],
            ]);
    }

    protected function fallbackHtml(string $documentType, array $vars): string
    {
        $title = Str::headline(str_replace('_', ' ', $documentType));

        return '<html><body style="font-family:Inter,sans-serif;padding:20px"><h2>'.e($vars['hospital_name'] ?? 'CosyClinic').'</h2>'
            .'<p><strong>'.$title.'</strong> — No published template found. Configure in Admin → Print Management.</p></body></html>';
    }
}
