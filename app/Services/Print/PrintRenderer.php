<?php

namespace App\Services\Print;

use App\Models\PaperSize;
use App\Models\PrintTemplate;

class PrintRenderer
{
    public function render(PrintTemplate $template, array $vars, PaperSize $paper): string
    {
        $layout = $template->layout ?? [];
        $theme = array_merge([
            'primary' => $vars['primary_color'] ?? '#0ea5e9',
            'secondary' => $vars['secondary_color'] ?? '#06b6d4',
            'text' => '#0f172a',
            'background' => '#ffffff',
            'font' => 'Inter, Arial, sans-serif',
        ], $template->theme ?? []);

        $margins = $paper->margins ?? ['top' => 8, 'right' => 8, 'bottom' => 8, 'left' => 8];
        $width = $paper->width_mm;
        $height = $paper->height_mm;

        $headerHtml = $this->renderSection($template->header, $vars, $theme, 'header');
        $footerHtml = $this->renderSection($template->footer, $vars, $theme, 'footer');
        $bodyHtml = $this->renderComponents($layout['components'] ?? [], $vars, $theme);

        $css = $this->buildCss($paper, $theme, $margins, $layout);

        return <<<HTML
<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>{$css}</style></head>
<body class="pt-body">
<div class="pt-page" style="width:{$width}mm;min-height:{$height}mm">
{$headerHtml}
<div class="pt-canvas">{$bodyHtml}</div>
{$footerHtml}
</div>
</body></html>
HTML;
    }

    protected function renderSection(?array $section, array $vars, array $theme, string $type): string
    {
        if (! $section || empty($section['enabled'])) {
            return '';
        }

        $parts = [];
        $align = $section['align'] ?? 'center';

        if (! empty($section['logo']) && ! empty($vars['logo'])) {
            $size = (int) ($section['logo_size'] ?? 48);
            $parts[] = '<img src="'.e($vars['logo']).'" class="pt-logo" style="height:'.$size.'px" alt="Logo">';
        }
        if (! empty($section['hospital_name'])) {
            $parts[] = '<div class="pt-h-name">'.e($vars['hospital_name'] ?? '').'</div>';
        }
        if (! empty($section['hospital_address'])) {
            $parts[] = '<div class="pt-h-addr">'.e($vars['hospital_address'] ?? '').'</div>';
        }
        if (! empty($section['phone'])) {
            $parts[] = '<div class="pt-h-meta">'.e($vars['hospital_phone'] ?? '').'</div>';
        }
        if (! empty($section['email'])) {
            $parts[] = '<div class="pt-h-meta">'.e($vars['hospital_email'] ?? '').'</div>';
        }
        if (! empty($section['date'])) {
            $parts[] = '<div class="pt-h-meta">'.e($vars['date'] ?? '').'</div>';
        }
        if (! empty($section['printed_by'])) {
            $parts[] = '<div class="pt-h-meta">By: '.e($vars['printed_by'] ?? '').'</div>';
        }
        if ($type === 'footer') {
            if (! empty($section['thank_you'])) {
                $parts[] = '<div class="pt-f-thanks">'.e($section['thank_you_text'] ?? 'Thank you for visiting us.').'</div>';
            }
            if (! empty($section['footer_text'])) {
                $parts[] = '<div class="pt-f-text">'.e($vars['footer_note'] ?? '').'</div>';
            }
            if (! empty($section['terms'])) {
                $parts[] = '<div class="pt-f-terms">'.e($vars['terms_conditions'] ?? '').'</div>';
            }
            if (! empty($section['page_number'])) {
                $parts[] = '<div class="pt-f-page">Page 1</div>';
            }
        }

        if (empty($parts)) {
            return '';
        }

        return '<div class="pt-'.$type.'" style="text-align:'.$align.'">'.implode('', $parts).'</div>';
    }

    protected function renderComponents(array $components, array $vars, array $theme): string
    {
        usort($components, fn ($a, $b) => ($a['z'] ?? 0) <=> ($b['z'] ?? 0));

        $html = '';
        foreach ($components as $c) {
            $html .= $this->renderComponent($c, $vars, $theme);
        }

        return $html;
    }

    protected function renderComponent(array $c, array $vars, array $theme): string
    {
        $type = $c['type'] ?? 'text';
        $props = $c['props'] ?? [];
        $style = $this->componentStyle($c, $props, $theme);

        return match ($type) {
            'heading', 'text', 'paragraph' => $this->renderText($props, $vars, $style, $type),
            'divider', 'line' => '<div style="'.$style.';border-top:'.($props['thickness'] ?? 2).'px solid '.($props['color'] ?? $theme['primary']).'"></div>',
            'rectangle' => '<div style="'.$style.';background:'.($props['background'] ?? 'transparent').';border:'.($props['border'] ?? '1px solid #e2e8f0').'"></div>',
            'image', 'logo' => $this->renderImage($props, $vars, $style),
            'qr' => '<div style="'.$style.'">'.$this->replaceVars($props['content'] ?? '{{qr}}', $vars).'</div>',
            'barcode' => '<div style="'.$style.';font-family:monospace;font-size:14px;letter-spacing:2px">'.e($this->plainVar($props['content'] ?? '{{barcode}}', $vars)).'</div>',
            'table', 'items_table' => '<div style="'.$style.'">'.$this->replaceVars($props['content'] ?? '{{items_table}}', $vars).'</div>',
            default => '<div style="'.$style.'">'.e($this->plainVar($props['content'] ?? '', $vars)).'</div>',
        };
    }

    protected function renderText(array $props, array $vars, string $style, string $type): string
    {
        $content = $this->replaceVars($props['content'] ?? '', $vars);
        $tag = $type === 'heading' ? 'div' : 'div';
        $extra = $type === 'heading' ? 'font-weight:800;' : '';

        return '<'.$tag.' style="'.$style.';'.$extra.'">'.$content.'</'.$tag.'>';
    }

    protected function renderImage(array $props, array $vars, string $style): string
    {
        $src = $props['src'] ?? '';
        if (str_contains($src, '{{')) {
            $src = $this->plainVar($src, $vars);
        }

        if (! $src) {
            return '';
        }

        return '<img src="'.e($src).'" style="'.$style.';object-fit:contain" alt="">';
    }

    protected function componentStyle(array $c, array $props, array $theme): string
    {
        $x = $c['x'] ?? 0;
        $y = $c['y'] ?? 0;
        $w = $c['width'] ?? 100;
        $h = $c['height'] ?? 'auto';
        $unit = $c['unit'] ?? '%';

        $styles = [
            'position' => ($unit === '%' || isset($c['absolute'])) ? 'absolute' : 'relative',
            'left' => $unit === '%' ? $x.'%' : $x.'mm',
            'top' => $unit === '%' ? $y.'%' : $y.'mm',
            'width' => $unit === '%' ? $w.'%' : $w.'mm',
            'font-family' => $props['font'] ?? $theme['font'] ?? 'Inter, sans-serif',
            'font-size' => ($props['fontSize'] ?? 12).'px',
            'font-weight' => $props['fontWeight'] ?? 'normal',
            'color' => $props['color'] ?? $theme['text'] ?? '#0f172a',
            'text-align' => $props['align'] ?? 'left',
            'line-height' => (string) ($props['lineHeight'] ?? 1.4),
            'opacity' => (string) ($props['opacity'] ?? 1),
            'border-radius' => ($props['borderRadius'] ?? 0).'px',
            'padding' => ($props['padding'] ?? 0).'px',
            'box-sizing' => 'border-box',
        ];

        if ($h !== 'auto') {
            $styles['height'] = $unit === '%' ? $h.'%' : $h.'mm';
        }

        if (! empty($props['background'])) {
            $styles['background'] = $props['background'];
        }

        return collect($styles)->map(fn ($v, $k) => "{$k}:{$v}")->implode(';');
    }

    protected function replaceVars(string $content, array $vars): string
    {
        return preg_replace_callback('/\{\{(\w+)\}\}/', function ($m) use ($vars) {
            $key = $m[1];
            $val = $vars[$key] ?? '';

            if (in_array($key, ['qr', 'items_table']) && is_string($val) && str_contains($val, '<')) {
                return $val;
            }

            return e((string) $val);
        }, $content);
    }

    protected function plainVar(string $content, array $vars): string
    {
        if (preg_match('/\{\{(\w+)\}\}/', $content, $m)) {
            return (string) ($vars[$m[1]] ?? '');
        }

        return $content;
    }

    protected function buildCss(PaperSize $paper, array $theme, array $margins, array $layout): string
    {
        $bg = $layout['background'] ?? $theme['background'] ?? '#fff';

        return "
        @page { size: {$paper->width_mm}mm {$paper->height_mm}mm; margin: 0; }
        * { box-sizing: border-box; }
        body.pt-body { margin:0; padding:0; background:#f1f5f9; font-family:{$theme['font']}; }
        .pt-page { position:relative; margin:0 auto; background:{$bg}; padding:{$margins['top']}mm {$margins['right']}mm {$margins['bottom']}mm {$margins['left']}mm; overflow:hidden; }
        .pt-canvas { position:relative; min-height:60mm; width:100%; }
        .pt-header { border-bottom:2px solid {$theme['primary']}; padding-bottom:6px; margin-bottom:8px; }
        .pt-footer { border-top:1px dashed #cbd5e1; padding-top:6px; margin-top:10px; font-size:10px; color:#64748b; }
        .pt-h-name { font-size:16px; font-weight:800; color:{$theme['primary']}; }
        .pt-h-addr,.pt-h-meta { font-size:10px; color:#64748b; margin-top:2px; }
        .pt-f-thanks { font-weight:700; color:{$theme['primary']}; }
        .pt-table { width:100%; border-collapse:collapse; font-size:11px; }
        .pt-table th,.pt-table td { border:1px solid #e2e8f0; padding:4px 6px; }
        .pt-table th { background:#f8fafc; }
        @media print {
            body.pt-body { background:#fff; }
            .pt-page { margin:0; box-shadow:none; }
        }
        ";
    }
}
