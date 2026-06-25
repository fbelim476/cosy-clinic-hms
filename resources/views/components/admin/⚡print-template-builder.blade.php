<?php

use App\Models\FontLibrary;
use App\Models\PaperSize;
use App\Models\PrintTemplate;
use App\Services\Print\PrintEngineService;
use App\Services\Print\PrintTemplateService;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{
    #[Locked]
    public int $templateId;

    public string $name = '';
    public string $document_type = '';
    public ?int $paper_size_id = null;
    public string $status = 'draft';
    public array $layout = ['components' => []];
    public array $header = [];
    public array $footer = [];
    public array $theme = [];
    public ?string $previewHtml = null;
    public ?int $selectedIndex = null;

    public function mount(PrintTemplate $template): void
    {
        $this->templateId = $template->id;
        $this->name = $template->name;
        $this->document_type = $template->document_type;
        $this->paper_size_id = $template->paper_size_id;
        $this->status = $template->status;
        $this->layout = $template->layout ?? ['components' => []];
        $this->header = $template->header ?? app(PrintTemplateService::class)->defaultHeader();
        $this->footer = $template->footer ?? app(PrintTemplateService::class)->defaultFooter();
        $this->theme = $template->theme ?? app(PrintTemplateService::class)->defaultTheme();
    }

    public function save(bool $newVersion = false): void
    {
        $template = PrintTemplate::findOrFail($this->templateId);
        app(PrintTemplateService::class)->update($template, [
            'name' => $this->name,
            'paper_size_id' => $this->paper_size_id,
            'layout' => $this->layout,
            'header' => $this->header,
            'footer' => $this->footer,
            'theme' => $this->theme,
        ], $newVersion);

        $this->dispatch('saved');
    }

    public function publish(): void
    {
        $this->save();
        app(PrintTemplateService::class)->publish(PrintTemplate::findOrFail($this->templateId));
        $this->status = 'published';
        session()->flash('success', 'Template published successfully.');
    }

    public function refreshPreview(): void
    {
        $template = PrintTemplate::findOrFail($this->templateId);
        $template->layout = $this->layout;
        $template->header = $this->header;
        $template->footer = $this->footer;
        $template->theme = $this->theme;
        $template->paper_size_id = $this->paper_size_id;
        $this->previewHtml = app(PrintEngineService::class)->preview($template);
    }

    public function with(): array
    {
        return [
            'paperSizes' => PaperSize::where('is_active', true)->orderBy('name')->get(),
            'fonts' => FontLibrary::where('is_active', true)->orderBy('name')->get(),
            'variables' => $this->variableList(),
            'palette' => $this->palette(),
            'paper' => PaperSize::find($this->paper_size_id),
        ];
    }

    protected function palette(): array
    {
        return [
            ['type' => 'heading', 'label' => 'Heading', 'icon' => 'ti-h-1'],
            ['type' => 'text', 'label' => 'Text', 'icon' => 'ti-letter-t'],
            ['type' => 'paragraph', 'label' => 'Paragraph', 'icon' => 'ti-align-left'],
            ['type' => 'divider', 'label' => 'Divider', 'icon' => 'ti-separator-horizontal'],
            ['type' => 'qr', 'label' => 'QR Code', 'icon' => 'ti-qrcode'],
            ['type' => 'barcode', 'label' => 'Barcode', 'icon' => 'ti-barcode'],
            ['type' => 'image', 'label' => 'Image', 'icon' => 'ti-photo'],
            ['type' => 'rectangle', 'label' => 'Rectangle', 'icon' => 'ti-square'],
            ['type' => 'items_table', 'label' => 'Table', 'icon' => 'ti-table'],
        ];
    }

    protected function variableList(): array
    {
        return [
            '{{hospital_name}}', '{{hospital_address}}', '{{hospital_phone}}', '{{patient_name}}',
            '{{patient_id}}', '{{patient_age}}', '{{patient_gender}}', '{{patient_mobile}}',
            '{{doctor_name}}', '{{department}}', '{{visit_no}}', '{{token}}', '{{invoice_no}}',
            '{{grand_total}}', '{{paid_amount}}', '{{due_amount}}', '{{date}}', '{{time}}',
            '{{qr}}', '{{barcode}}', '{{items_table}}',
        ];
    }
};
?>

<div class="cc-print-builder" x-data="printBuilder(@js($layout), @js($palette), @js($variables))"
     x-on:builder-updated.window="syncFromLivewire($event.detail)"
     wire:ignore.self>
    <div class="cc-pb-toolbar no-print">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('admin.print.templates') }}" class="btn btn-ghost-secondary btn-sm"><i class="ti ti-arrow-left"></i></a>
            <input type="text" wire:model.blur="name" class="form-control form-control-sm cc-pb-title-input" style="width:220px">
            <select wire:model.live="paper_size_id" class="form-select form-select-sm" style="width:auto">
                @foreach($paperSizes as $ps)
                    <option value="{{ $ps->id }}">{{ $ps->name }}</option>
                @endforeach
            </select>
            <span class="badge {{ $status === 'published' ? 'bg-green' : 'bg-yellow' }}">{{ ucfirst($status) }}</span>
        </div>
        <div class="d-flex gap-1 flex-wrap">
            <button type="button" class="btn btn-sm btn-ghost-secondary" @click="undo()" :disabled="!canUndo"><i class="ti ti-arrow-back-up"></i></button>
            <button type="button" class="btn btn-sm btn-ghost-secondary" @click="redo()" :disabled="!canRedo"><i class="ti ti-arrow-forward-up"></i></button>
            <button type="button" class="btn btn-sm btn-ghost-secondary" wire:click="refreshPreview"><i class="ti ti-eye"></i> Preview</button>
            <button type="button" class="btn btn-sm btn-primary" wire:click="save"><i class="ti ti-device-floppy"></i> Save</button>
            <button type="button" class="btn btn-sm btn-success" wire:click="publish"><i class="ti ti-upload"></i> Publish</button>
        </div>
    </div>

    <div class="cc-pb-workspace">
        <aside class="cc-pb-panel cc-pb-palette">
            <h6>Elements</h6>
            <div class="cc-pb-palette-grid">
                @foreach($palette as $item)
                    <button type="button" class="cc-pb-palette-item" @click="addComponent('{{ $item['type'] }}')">
                        <i class="ti {{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </button>
                @endforeach
            </div>
            <h6 class="mt-3">Variables</h6>
            <div class="cc-pb-vars">
                @foreach($variables as $var)
                    <button type="button" class="cc-pb-var-chip" @click="insertVariable('{{ $var }}')">{{ $var }}</button>
                @endforeach
            </div>
        </aside>

        <main class="cc-pb-canvas-wrap">
            <div class="cc-pb-canvas-toolbar">
                <button type="button" class="btn btn-sm" :class="zoom===1?'btn-primary':'btn-ghost-secondary'" @click="zoom=1">100%</button>
                <button type="button" class="btn btn-sm" :class="zoom===0.75?'btn-primary':'btn-ghost-secondary'" @click="zoom=0.75">75%</button>
                <button type="button" class="btn btn-sm" :class="snap?'btn-primary':'btn-ghost-secondary'" @click="snap=!snap">Snap</button>
            </div>
            <div class="cc-pb-canvas-scroll">
                <div class="cc-pb-canvas" :style="`transform:scale(${zoom});width:{{ $paper?->width_mm ?? 80 }}mm;min-height:{{ $paper?->height_mm ?? 200 }}mm`"
                     @click.self="selected=null">
                    <template x-for="(comp, idx) in components" :key="comp.id">
                        <div class="cc-pb-element"
                             :class="{'selected': selected===idx}"
                             :style="elementStyle(comp)"
                             @mousedown.stop="startDrag($event, idx)"
                             @click.stop="select(idx)">
                            <div class="cc-pb-element-inner" x-html="renderPreview(comp)"></div>
                            <div class="cc-pb-resize" @mousedown.stop="startResize($event, idx)"></div>
                        </div>
                    </template>
                </div>
            </div>
        </main>

        <aside class="cc-pb-panel cc-pb-props">
            <h6>Properties</h6>
            <template x-if="selected !== null && components[selected]">
                <div class="cc-pb-props-form">
                    <div class="mb-2">
                        <label class="form-label">Content</label>
                        <textarea class="form-control form-control-sm" rows="3" x-model="components[selected].props.content" @input="pushHistory(); syncLivewire()"></textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col-6"><label class="form-label">X %</label><input type="number" class="form-control form-control-sm" x-model.number="components[selected].x" @change="pushHistory(); syncLivewire()"></div>
                        <div class="col-6"><label class="form-label">Y %</label><input type="number" class="form-control form-control-sm" x-model.number="components[selected].y" @change="pushHistory(); syncLivewire()"></div>
                        <div class="col-6"><label class="form-label">Width %</label><input type="number" class="form-control form-control-sm" x-model.number="components[selected].width" @change="pushHistory(); syncLivewire()"></div>
                        <div class="col-6"><label class="form-label">Font Size</label><input type="number" class="form-control form-control-sm" x-model.number="components[selected].props.fontSize" @change="pushHistory(); syncLivewire()"></div>
                        <div class="col-12">
                            <label class="form-label">Font</label>
                            <select class="form-select form-select-sm" x-model="components[selected].props.font" @change="pushHistory(); syncLivewire()">
                                @foreach($fonts as $font)
                                    <option value="{{ $font->family }}">{{ $font->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6"><label class="form-label">Color</label><input type="color" class="form-control form-control-color form-control-sm" x-model="components[selected].props.color" @change="pushHistory(); syncLivewire()"></div>
                        <div class="col-6"><label class="form-label">Align</label>
                            <select class="form-select form-select-sm" x-model="components[selected].props.align" @change="pushHistory(); syncLivewire()">
                                <option value="left">Left</option><option value="center">Center</option><option value="right">Right</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-ghost-danger w-100 mt-3" @click="removeSelected()"><i class="ti ti-trash"></i> Remove</button>
                </div>
            </template>
            <template x-if="selected === null">
                <p class="text-muted small">Select an element on the canvas to edit properties.</p>
            </template>
        </aside>
    </div>

    @if($previewHtml)
        <div class="modal modal-blur show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)" wire:click.self="previewHtml = null">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Live Preview</h5>
                        <button type="button" class="btn-close" wire:click="$set('previewHtml', null)"></button>
                    </div>
                    <div class="modal-body p-0">{!! $previewHtml !!}</div>
                </div>
            </div>
        </div>
    @endif

    @script
    <script>
        Alpine.data('printBuilder', (initialLayout, palette, variables) => ({
            components: initialLayout.components || [],
            selected: null,
            zoom: 1,
            snap: true,
            history: [],
            future: [],
            canUndo: false,
            canRedo: false,
            drag: null,

            init() {
                this.pushHistory(false);
                document.addEventListener('keydown', (e) => {
                    if ((e.ctrlKey || e.metaKey) && e.key === 'z') { e.preventDefault(); this.undo(); }
                    if ((e.ctrlKey || e.metaKey) && e.key === 'y') { e.preventDefault(); this.redo(); }
                    if (e.key === 'Delete' && this.selected !== null) this.removeSelected();
                });
                setInterval(() => this.syncLivewire(), 30000);
            },

            uid() { return 'c' + Date.now() + Math.random().toString(36).slice(2, 6); },

            addComponent(type) {
                const defaults = {
                    heading: { content: 'Heading', fontSize: 18, fontWeight: '800', align: 'center', color: '#0ea5e9' },
                    text: { content: 'Text block', fontSize: 11, align: 'left', color: '#0f172a' },
                    paragraph: { content: 'Paragraph text', fontSize: 10, lineHeight: 1.5 },
                    qr: { content: '@{{qr}}', align: 'center' },
                    barcode: { content: '@{{barcode}}', align: 'center' },
                    items_table: { content: '@{{items_table}}' },
                    divider: { color: '#0ea5e9', thickness: 2 },
                    image: { src: '@{{logo}}' },
                    rectangle: { background: '#f0f9ff', border: '1px solid #bae6fd' },
                };
                const y = this.components.length * 8 + 2;
                this.components.push({
                    id: this.uid(), type, x: 5, y: Math.min(y, 80), width: 90, unit: '%',
                    props: defaults[type] || { content: 'Element' }
                });
                this.selected = this.components.length - 1;
                this.pushHistory();
                this.syncLivewire();
            },

            select(idx) { this.selected = idx; },
            removeSelected() {
                if (this.selected === null) return;
                this.components.splice(this.selected, 1);
                this.selected = null;
                this.pushHistory();
                this.syncLivewire();
            },

            insertVariable(v) {
                if (this.selected === null) return;
                const c = this.components[this.selected];
                c.props.content = (c.props.content || '') + v;
                this.pushHistory();
                this.syncLivewire();
            },

            elementStyle(comp) {
                return `left:${comp.x}%;top:${comp.y}%;width:${comp.width}%;`;
            },

            renderPreview(comp) {
                const p = comp.props || {};
                const content = (p.content || comp.type).replace(/\{\{(\w+)\}\}/g, '<span class="text-primary">@{{$1}}</span>');
                if (comp.type === 'divider') return '<hr style="border-color:' + (p.color||'#0ea5e9') + '">';
                if (comp.type === 'qr') return '<div style="border:2px dashed #94a3b8;width:60px;height:60px;display:flex;align-items:center;justify-content:center;font-size:10px;margin:0 auto">QR</div>';
                if (comp.type === 'items_table') return '<table style="width:100%;font-size:9px;border:1px solid #e2e8f0"><tr><td>Table</td></tr></table>';
                const fs = p.fontSize || 12;
                const fw = p.fontWeight || 'normal';
                const align = p.align || 'left';
                const color = p.color || '#0f172a';
                return `<div style="font-size:${fs}px;font-weight:${fw};text-align:${align};color:${color};white-space:pre-wrap">${content}</div>`;
            },

            startDrag(e, idx) {
                this.selected = idx;
                const canvas = e.target.closest('.cc-pb-canvas');
                const rect = canvas.getBoundingClientRect();
                this.drag = { idx, startX: e.clientX, startY: e.clientY, ox: this.components[idx].x, oy: this.components[idx].y, rect };
                const move = (ev) => {
                    if (!this.drag) return;
                    const dx = ((ev.clientX - this.drag.startX) / this.drag.rect.width) * 100;
                    const dy = ((ev.clientY - this.drag.startY) / this.drag.rect.height) * 100;
                    let nx = this.drag.ox + dx;
                    let ny = this.drag.oy + dy;
                    if (this.snap) { nx = Math.round(nx / 5) * 5; ny = Math.round(ny / 5) * 5; }
                    this.components[this.drag.idx].x = Math.max(0, Math.min(95, nx));
                    this.components[this.drag.idx].y = Math.max(0, Math.min(95, ny));
                };
                const up = () => {
                    document.removeEventListener('mousemove', move);
                    document.removeEventListener('mouseup', up);
                    if (this.drag) { this.pushHistory(); this.syncLivewire(); }
                    this.drag = null;
                };
                document.addEventListener('mousemove', move);
                document.addEventListener('mouseup', up);
            },

            startResize(e, idx) {
                e.stopPropagation();
                const canvas = e.target.closest('.cc-pb-canvas');
                const rect = canvas.getBoundingClientRect();
                const startW = this.components[idx].width;
                const startX = e.clientX;
                const move = (ev) => {
                    const dw = ((ev.clientX - startX) / rect.width) * 100;
                    this.components[idx].width = Math.max(10, Math.min(100, startW + dw));
                };
                const up = () => {
                    document.removeEventListener('mousemove', move);
                    document.removeEventListener('mouseup', up);
                    this.pushHistory();
                    this.syncLivewire();
                };
                document.addEventListener('mousemove', move);
                document.addEventListener('mouseup', up);
            },

            pushHistory(record = true) {
                if (record) {
                    this.history.push(JSON.stringify(this.components));
                    if (this.history.length > 50) this.history.shift();
                    this.future = [];
                }
                this.canUndo = this.history.length > 1;
                this.canRedo = this.future.length > 0;
            },

            undo() {
                if (this.history.length <= 1) return;
                this.future.push(this.history.pop());
                this.components = JSON.parse(this.history[this.history.length - 1]);
                this.canUndo = this.history.length > 1;
                this.canRedo = true;
                this.syncLivewire();
            },

            redo() {
                if (!this.future.length) return;
                const state = this.future.pop();
                this.history.push(state);
                this.components = JSON.parse(state);
                this.canUndo = true;
                this.canRedo = this.future.length > 0;
                this.syncLivewire();
            },

            syncLivewire() {
                $wire.set('layout', { components: this.components });
            },

            syncFromLivewire(detail) {
                if (detail?.components) this.components = detail.components;
            }
        }));
    </script>
    @endscript
</div>
