<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\FontLibrary;
use App\Models\HospitalBranding;
use App\Models\PaperSize;
use App\Models\PrintSetting;
use App\Models\PrintTemplate;
use App\Models\PrinterProfile;
use Illuminate\Database\Seeder;

class PrintManagementSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::first();
        $branchId = $branch?->id;

        $papers = [
            ['name' => 'A4', 'slug' => 'a4', 'category' => 'standard', 'width_mm' => 210, 'height_mm' => 297],
            ['name' => 'A5', 'slug' => 'a5', 'category' => 'standard', 'width_mm' => 148, 'height_mm' => 210],
            ['name' => 'Letter', 'slug' => 'letter', 'category' => 'standard', 'width_mm' => 216, 'height_mm' => 279],
            ['name' => 'Legal', 'slug' => 'legal', 'category' => 'standard', 'width_mm' => 216, 'height_mm' => 356],
            ['name' => 'Thermal 58mm', 'slug' => 'thermal-58', 'category' => 'thermal', 'width_mm' => 58, 'height_mm' => 200],
            ['name' => 'Thermal 80mm', 'slug' => 'thermal-80', 'category' => 'thermal', 'width_mm' => 80, 'height_mm' => 200],
            ['name' => '3 Inch', 'slug' => '3-inch', 'category' => 'thermal', 'width_mm' => 76, 'height_mm' => 150],
            ['name' => '4 Inch', 'slug' => '4-inch', 'category' => 'thermal', 'width_mm' => 102, 'height_mm' => 150],
            ['name' => 'Patient Card', 'slug' => 'patient-card', 'category' => 'card', 'width_mm' => 86, 'height_mm' => 54],
            ['name' => 'ID Card', 'slug' => 'id-card', 'category' => 'card', 'width_mm' => 85.6, 'height_mm' => 53.98],
            ['name' => 'Label', 'slug' => 'label', 'category' => 'label', 'width_mm' => 50, 'height_mm' => 25],
        ];

        foreach ($papers as $p) {
            PaperSize::updateOrCreate(['slug' => $p['slug']], array_merge($p, [
                'margins' => ['top' => 5, 'right' => 5, 'bottom' => 5, 'left' => 5],
                'orientation' => 'portrait',
                'scale' => 100,
                'dpi' => 96,
                'is_system' => true,
                'is_active' => true,
            ]));
        }

        $thermal80 = PaperSize::where('slug', 'thermal-80')->first();
        $a4 = PaperSize::where('slug', 'a4')->first();
        $patientCard = PaperSize::where('slug', 'patient-card')->first();

        $profiles = [
            ['name' => 'PDF Export', 'slug' => 'pdf', 'type' => 'pdf', 'paper_size_id' => $a4?->id, 'is_default' => true],
            ['name' => 'Laser Printer', 'slug' => 'laser', 'type' => 'laser', 'paper_size_id' => $a4?->id],
            ['name' => 'Thermal Printer', 'slug' => 'thermal', 'type' => 'thermal', 'paper_size_id' => $thermal80?->id],
            ['name' => 'Inkjet Printer', 'slug' => 'inkjet', 'type' => 'inkjet', 'paper_size_id' => $a4?->id],
            ['name' => 'Network Printer', 'slug' => 'network', 'type' => 'network', 'paper_size_id' => $a4?->id],
        ];

        foreach ($profiles as $pr) {
            PrinterProfile::updateOrCreate(['slug' => $pr['slug']], array_merge($pr, [
                'settings' => ['margins' => ['top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 10], 'scale' => 100, 'orientation' => 'portrait', 'copies' => 1],
                'is_active' => true,
            ]));
        }

        HospitalBranding::updateOrCreate(
            ['branch_id' => $branchId],
            [
                'hospital_name' => 'CosyClinic Charitable Trust',
                'hospital_address' => 'Behind Bus Station, Railway Crossing, Chha Port - 361330',
                'hospital_phone' => '+91 98765 43210',
                'hospital_email' => 'info@cosyclinic.org',
                'website' => 'www.cosyclinic.org',
                'gst_number' => '27AAAAA0000A1Z5',
                'registration_number' => 'HMS-REG-2024-001',
                'license_number' => 'LIC-MH-2024-88',
                'emergency_contact' => '+91 112',
                'footer_note' => 'Please wait in the waiting area until your token is called. Keep this slip for reference.',
                'terms_conditions' => 'All services are subject to hospital policies.',
                'tagline' => 'Your Health, Our Priority',
                'colors' => ['primary' => '#0ea5e9', 'secondary' => '#06b6d4', 'accent' => '#3b82f6'],
                'header_config' => ['enabled' => true, 'logo' => false, 'hospital_name' => true, 'hospital_address' => true],
                'footer_config' => ['enabled' => true, 'thank_you' => true, 'footer_text' => true],
                'qr_settings' => ['size' => 80, 'foreground' => '#000000', 'background' => '#ffffff', 'error_correction' => 'M'],
                'barcode_settings' => ['type' => 'CODE128', 'width' => 2, 'height' => 40, 'show_text' => true],
            ]
        );

        $fonts = ['Roboto', 'Inter', 'Poppins', 'Open Sans', 'Arial', 'Courier', 'Times New Roman', 'Noto Sans'];
        foreach ($fonts as $font) {
            FontLibrary::updateOrCreate(
                ['family' => $font],
                ['name' => $font, 'source' => in_array($font, ['Arial', 'Courier', 'Times New Roman']) ? 'system' : 'google', 'is_system' => true, 'is_active' => true]
            );
        }

        PrintSetting::updateOrCreate(['branch_id' => $branchId, 'key' => 'pdf_engine'], ['value' => 'dompdf', 'group' => 'engine']);
        PrintSetting::updateOrCreate(['branch_id' => $branchId, 'key' => 'default_print_mode'], ['value' => 'browser', 'group' => 'general']);
        PrintSetting::updateOrCreate(['branch_id' => $branchId, 'key' => 'auto_print'], ['value' => '1', 'group' => 'general']);

        $this->seedTemplates($branchId, $thermal80, $a4, $patientCard);
    }

    protected function seedTemplates(?int $branchId, ?PaperSize $thermal, ?PaperSize $a4, ?PaperSize $card): void
    {
        $header = ['enabled' => true, 'hospital_name' => true, 'hospital_address' => true, 'align' => 'center'];
        $footer = ['enabled' => true, 'thank_you' => true, 'thank_you_text' => 'Thank you for visiting us.', 'footer_text' => true, 'align' => 'center'];
        $theme = ['primary' => '#0ea5e9', 'secondary' => '#06b6d4', 'text' => '#0f172a', 'background' => '#ffffff', 'font' => 'Inter, Arial, sans-serif'];

        $templates = [
            [
                'name' => 'OPD Token Slip',
                'slug' => 'opd-token-thermal',
                'document_type' => 'opd_token',
                'paper_size_id' => $thermal?->id,
                'is_default' => true,
                'layout' => ['components' => [
                    ['id' => 't1', 'type' => 'heading', 'x' => 5, 'y' => 2, 'width' => 90, 'unit' => '%', 'props' => ['content' => 'OPD REGISTRATION SLIP', 'fontSize' => 11, 'align' => 'center', 'color' => '#0369a1', 'fontWeight' => '800']],
                    ['id' => 't2', 'type' => 'text', 'x' => 5, 'y' => 12, 'width' => 90, 'unit' => '%', 'props' => ['content' => 'YOUR TOKEN', 'fontSize' => 9, 'align' => 'center', 'color' => '#64748b']],
                    ['id' => 't3', 'type' => 'heading', 'x' => 5, 'y' => 18, 'width' => 90, 'unit' => '%', 'props' => ['content' => '{{token}}', 'fontSize' => 36, 'align' => 'center', 'color' => '#0284c7', 'fontWeight' => '900']],
                    ['id' => 't4', 'type' => 'paragraph', 'x' => 5, 'y' => 38, 'width' => 90, 'unit' => '%', 'props' => ['content' => "Visit: {{visit_no}}\nDate: {{datetime}}\nPatient: {{patient_name}} ({{patient_id}})\nMobile: {{patient_mobile}}\nAge/Sex: {{patient_age}} / {{patient_gender}}\nDept: {{department}}\nDoctor: {{doctor_name}}", 'fontSize' => 9, 'lineHeight' => 1.5]],
                    ['id' => 't5', 'type' => 'qr', 'x' => 30, 'y' => 62, 'width' => 40, 'unit' => '%', 'props' => ['content' => '{{qr}}', 'align' => 'center']],
                ]],
            ],
            [
                'name' => 'Patient Card',
                'slug' => 'patient-card-default',
                'document_type' => 'patient_card',
                'paper_size_id' => $card?->id,
                'is_default' => true,
                'layout' => ['components' => [
                    ['id' => 'c1', 'type' => 'text', 'x' => 5, 'y' => 5, 'width' => 55, 'unit' => '%', 'props' => ['content' => '{{hospital_name}}', 'fontSize' => 10, 'color' => '#206bc4', 'fontWeight' => '700']],
                    ['id' => 'c2', 'type' => 'heading', 'x' => 5, 'y' => 18, 'width' => 55, 'unit' => '%', 'props' => ['content' => '{{patient_id}}', 'fontSize' => 14, 'fontWeight' => '800']],
                    ['id' => 'c3', 'type' => 'text', 'x' => 5, 'y' => 32, 'width' => 90, 'unit' => '%', 'props' => ['content' => '{{patient_name}}', 'fontSize' => 11, 'fontWeight' => '700']],
                    ['id' => 'c4', 'type' => 'text', 'x' => 5, 'y' => 44, 'width' => 90, 'unit' => '%', 'props' => ['content' => '{{patient_mobile}} | {{patient_age}} / {{patient_gender}}', 'fontSize' => 9]],
                    ['id' => 'c5', 'type' => 'qr', 'x' => 62, 'y' => 5, 'width' => 30, 'unit' => '%', 'props' => ['content' => '{{qr}}']],
                ]],
            ],
            [
                'name' => 'Invoice A4',
                'slug' => 'invoice-a4',
                'document_type' => 'invoice',
                'paper_size_id' => $a4?->id,
                'is_default' => true,
                'layout' => ['components' => [
                    ['id' => 'i1', 'type' => 'heading', 'x' => 5, 'y' => 2, 'width' => 90, 'unit' => '%', 'props' => ['content' => 'INVOICE', 'fontSize' => 18, 'align' => 'center', 'fontWeight' => '800']],
                    ['id' => 'i2', 'type' => 'text', 'x' => 5, 'y' => 10, 'width' => 90, 'unit' => '%', 'props' => ['content' => 'Invoice: {{invoice_no}} | Date: {{date}}', 'fontSize' => 10]],
                    ['id' => 'i3', 'type' => 'text', 'x' => 5, 'y' => 16, 'width' => 90, 'unit' => '%', 'props' => ['content' => 'Patient: {{patient_name}}', 'fontSize' => 10]],
                    ['id' => 'i4', 'type' => 'items_table', 'x' => 5, 'y' => 24, 'width' => 90, 'unit' => '%', 'props' => ['content' => '{{items_table}}']],
                ]],
            ],
            [
                'name' => 'Prescription A4',
                'slug' => 'prescription-a4',
                'document_type' => 'prescription',
                'paper_size_id' => $a4?->id,
                'is_default' => true,
                'layout' => ['components' => [
                    ['id' => 'p1', 'type' => 'heading', 'x' => 5, 'y' => 2, 'width' => 90, 'unit' => '%', 'props' => ['content' => 'PRESCRIPTION', 'fontSize' => 16, 'align' => 'center', 'fontWeight' => '800']],
                    ['id' => 'p2', 'type' => 'text', 'x' => 5, 'y' => 10, 'width' => 90, 'unit' => '%', 'props' => ['content' => "Patient: {{patient_name}} ({{patient_id}})\nDoctor: {{doctor_name}}\nDept: {{department}}\nDate: {{date}}", 'fontSize' => 10, 'lineHeight' => 1.6]],
                    ['id' => 'p3', 'type' => 'text', 'x' => 5, 'y' => 28, 'width' => 90, 'unit' => '%', 'props' => ['content' => 'Diagnosis: {{diagnosis}}', 'fontSize' => 10]],
                    ['id' => 'p4', 'type' => 'text', 'x' => 5, 'y' => 36, 'width' => 90, 'unit' => '%', 'props' => ['content' => 'Instructions: {{instructions}}', 'fontSize' => 10]],
                ]],
            ],
            [
                'name' => 'Pharmacy Bill',
                'slug' => 'pharmacy-bill-thermal',
                'document_type' => 'pharmacy_bill',
                'paper_size_id' => $thermal?->id,
                'is_default' => true,
                'layout' => ['components' => [
                    ['id' => 'ph1', 'type' => 'heading', 'x' => 5, 'y' => 2, 'width' => 90, 'unit' => '%', 'props' => ['content' => 'PHARMACY BILL', 'fontSize' => 12, 'align' => 'center', 'fontWeight' => '800']],
                    ['id' => 'ph2', 'type' => 'text', 'x' => 5, 'y' => 10, 'width' => 90, 'unit' => '%', 'props' => ['content' => 'Bill: {{bill_no}} | {{date}}', 'fontSize' => 9]],
                    ['id' => 'ph3', 'type' => 'items_table', 'x' => 5, 'y' => 18, 'width' => 90, 'unit' => '%', 'props' => ['content' => '{{items_table}}']],
                    ['id' => 'ph4', 'type' => 'text', 'x' => 5, 'y' => 70, 'width' => 90, 'unit' => '%', 'props' => ['content' => 'Total: {{grand_total}} | Paid: {{paid_amount}} | Due: {{due_amount}}', 'fontSize' => 10, 'fontWeight' => '700']],
                ]],
            ],
        ];

        foreach ($templates as $tpl) {
            PrintTemplate::updateOrCreate(
                ['slug' => $tpl['slug'], 'branch_id' => $branchId],
                array_merge($tpl, [
                    'branch_id' => $branchId,
                    'status' => PrintTemplate::STATUS_PUBLISHED,
                    'version' => 1,
                    'header' => $header,
                    'footer' => $footer,
                    'theme' => $theme,
                    'settings' => ['auto_print' => true],
                    'published_at' => now(),
                ])
            );
        }
    }
}
