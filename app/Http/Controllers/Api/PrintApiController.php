<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrintTemplate;
use App\Services\Print\PrintEngineService;
use App\Services\Print\PrintTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrintApiController extends Controller
{
    public function __construct(
        protected PrintTemplateService $templates,
        protected PrintEngineService $engine,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorizePrint('print.view');

        $query = PrintTemplate::with(['paperSize', 'printerProfile'])
            ->when($request->document_type, fn ($q, $t) => $q->where('document_type', $t))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->orderByDesc('updated_at');

        return response()->json($query->paginate(20));
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizePrint('print.create');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'document_type' => 'required|string|max:64',
            'paper_size_id' => 'nullable|exists:paper_sizes,id',
            'layout' => 'nullable|array',
            'header' => 'nullable|array',
            'footer' => 'nullable|array',
            'theme' => 'nullable|array',
        ]);

        $template = $this->templates->store($data);

        return response()->json($template->load(['paperSize', 'printerProfile']), 201);
    }

    public function update(Request $request, PrintTemplate $template): JsonResponse
    {
        $this->authorizePrint('print.edit');

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'document_type' => 'sometimes|string|max:64',
            'paper_size_id' => 'nullable|exists:paper_sizes,id',
            'layout' => 'nullable|array',
            'header' => 'nullable|array',
            'footer' => 'nullable|array',
            'theme' => 'nullable|array',
            'settings' => 'nullable|array',
        ]);

        $template = $this->templates->update($template, $data, $request->boolean('new_version'));

        return response()->json($template->load(['paperSize', 'printerProfile']));
    }

    public function destroy(PrintTemplate $template): JsonResponse
    {
        $this->authorizePrint('print.delete');
        $template->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function preview(Request $request): JsonResponse
    {
        $this->authorizePrint('print.preview');

        $data = $request->validate([
            'template_id' => 'nullable|exists:print_templates,id',
            'layout' => 'nullable|array',
            'header' => 'nullable|array',
            'footer' => 'nullable|array',
            'theme' => 'nullable|array',
            'paper_size_id' => 'nullable|exists:paper_sizes,id',
        ]);

        if (! empty($data['template_id'])) {
            $template = PrintTemplate::with('paperSize')->findOrFail($data['template_id']);
            if (! empty($data['layout'])) {
                $template->layout = $data['layout'];
            }
            if (! empty($data['header'])) {
                $template->header = $data['header'];
            }
            if (! empty($data['footer'])) {
                $template->footer = $data['footer'];
            }
            if (! empty($data['theme'])) {
                $template->theme = $data['theme'];
            }
        } else {
            $template = new PrintTemplate([
                'layout' => $data['layout'] ?? ['components' => []],
                'header' => $data['header'] ?? [],
                'footer' => $data['footer'] ?? [],
                'theme' => $data['theme'] ?? [],
            ]);
            if (! empty($data['paper_size_id'])) {
                $template->paper_size_id = $data['paper_size_id'];
                $template->load('paperSize');
            }
        }

        $html = $this->engine->preview($template);

        return response()->json(['html' => $html]);
    }

    public function export(PrintTemplate $template): JsonResponse
    {
        $this->authorizePrint('print.view');

        return response()->json($this->templates->export($template));
    }

    public function import(Request $request): JsonResponse
    {
        $this->authorizePrint('print.create');

        $payload = $request->validate([
            'name' => 'nullable|string',
            'document_type' => 'required|string',
            'layout' => 'required|array',
            'header' => 'nullable|array',
            'footer' => 'nullable|array',
            'theme' => 'nullable|array',
            'settings' => 'nullable|array',
        ]);

        $template = $this->templates->import($payload);

        return response()->json($template, 201);
    }

    protected function authorizePrint(string $permission): void
    {
        abort_unless(auth()->user()?->can($permission) || auth()->user()?->hasRole('super-admin'), 403);
    }
}
