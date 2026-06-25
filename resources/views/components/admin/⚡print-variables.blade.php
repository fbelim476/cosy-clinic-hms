<?php

use Livewire\Component;

new class extends Component
{
    public function with(): array
    {
        return ['variables' => [
            ['group' => 'Hospital', 'items' => ['{{hospital_name}}', '{{hospital_address}}', '{{hospital_phone}}', '{{hospital_email}}', '{{website}}', '{{gst_number}}', '{{registration_number}}']],
            ['group' => 'Patient', 'items' => ['{{patient_name}}', '{{patient_id}}', '{{patient_age}}', '{{patient_gender}}', '{{patient_mobile}}', '{{patient_address}}']],
            ['group' => 'Clinical', 'items' => ['{{doctor_name}}', '{{doctor_speciality}}', '{{department}}', '{{visit_no}}', '{{token}}', '{{appointment_no}}']],
            ['group' => 'Billing', 'items' => ['{{invoice_no}}', '{{bill_no}}', '{{receipt_no}}', '{{payment_mode}}', '{{grand_total}}', '{{paid_amount}}', '{{due_amount}}', '{{items_table}}']],
            ['group' => 'System', 'items' => ['{{date}}', '{{time}}', '{{datetime}}', '{{created_by}}', '{{printed_by}}', '{{branch}}', '{{qr}}', '{{barcode}}']],
        ]];
    }
};
?>

<div>
    @include('layouts.partials.print-nav')
    <div class="row g-3">
        @foreach($variables as $group)
            <div class="col-md-6 col-xl-4">
                <div class="card cc-glass-card h-100">
                    <div class="card-header"><h3 class="card-title">{{ $group['group'] }}</h3></div>
                    <div class="card-body d-flex flex-wrap gap-2">
                        @foreach($group['items'] as $var)
                            <code class="cc-pb-var-chip" style="cursor:pointer" onclick="navigator.clipboard.writeText('{{ $var }}')">{{ $var }}</code>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <p class="text-muted small mt-3">Click any variable to copy. Paste into template builder content fields.</p>
</div>
