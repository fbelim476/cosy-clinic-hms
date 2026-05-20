<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('gst_number', 30)->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('code', 20)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('employee_id', 50)->nullable()->unique();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('designation')->nullable();
            $table->string('signature_path')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->softDeletes();
        });

        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('registration_number')->nullable();
            $table->string('specialization')->nullable();
            $table->decimal('consultation_fee', 10, 2)->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id', 30)->unique();
            $table->string('barcode', 50)->nullable()->unique();
            $table->string('name');
            $table->string('mobile', 20)->index();
            $table->string('alternate_mobile', 20)->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->date('dob')->nullable();
            $table->string('blood_group', 10)->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('aadhaar', 20)->nullable();
            $table->string('occupation')->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->text('allergies')->nullable();
            $table->text('existing_diseases')->nullable();
            $table->string('photo_path')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('registered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['name', 'mobile']);
        });

        Schema::create('patient_visits', function (Blueprint $table) {
            $table->id();
            $table->string('visit_number', 30)->unique();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
            $table->foreignId('receptionist_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('token_number')->nullable();
            $table->unsignedInteger('queue_number')->nullable();
            $table->enum('visit_type', ['opd', 'emergency', 'follow_up', 'walk_in'])->default('opd');
            $table->enum('priority', ['normal', 'emergency'])->default('normal');
            $table->enum('status', [
                'registered', 'waiting', 'with_doctor', 'prescribed',
                'at_pharmacy', 'billing', 'lab_pending', 'completed', 'cancelled',
            ])->default('registered')->index();
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->string('bp', 20)->nullable();
            $table->string('sugar_rbs', 20)->nullable();
            $table->decimal('temperature', 5, 2)->nullable();
            $table->unsignedTinyInteger('spo2')->nullable();
            $table->text('symptoms')->nullable();
            $table->text('chief_complaint')->nullable();
            $table->string('referred_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('sent_to_doctor_at')->nullable();
            $table->timestamp('consultation_started_at')->nullable();
            $table->timestamp('sent_to_pharmacy_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'created_at']);
        });

        Schema::create('doctor_consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_visit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->text('diagnosis')->nullable();
            $table->text('clinical_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->text('public_notes')->nullable();
            $table->text('medical_advice')->nullable();
            $table->text('diet_plan')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->boolean('show_diagnosis_to_pharmacy')->default(true);
            $table->boolean('show_prescription_notes')->default(true);
            $table->boolean('show_reports')->default(false);
            $table->boolean('show_consultation_charges')->default(true);
            $table->boolean('show_instructions')->default(true);
            $table->decimal('consultation_charge', 10, 2)->default(0);
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('generic_name')->nullable();
            $table->string('sku', 50)->nullable()->unique();
            $table->string('barcode', 50)->nullable();
            $table->string('category')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('unit', 20)->default('strip');
            $table->decimal('mrp', 10, 2)->default(0);
            $table->decimal('selling_price', 10, 2)->default(0);
            $table->decimal('gst_percent', 5, 2)->default(0);
            $table->unsignedInteger('reorder_level')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('medicine_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number', 50);
            $table->date('expiry_date')->index();
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('purchase_price', 10, 2)->default(0);
            $table->decimal('selling_price', 10, 2)->default(0);
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->unique(['medicine_id', 'batch_number']);
        });

        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('prescription_number', 30)->unique();
            $table->foreignId('consultation_id')->constrained('doctor_consultations')->cascadeOnDelete();
            $table->foreignId('patient_visit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->enum('visibility', ['public', 'private'])->default('public');
            $table->text('instructions')->nullable();
            $table->enum('status', ['draft', 'active', 'dispensed', 'cancelled'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medicine_id')->nullable()->constrained()->nullOnDelete();
            $table->string('medicine_name');
            $table->string('dosage')->nullable();
            $table->string('frequency')->nullable();
            $table->boolean('morning')->default(false);
            $table->boolean('afternoon')->default(false);
            $table->boolean('night')->default(false);
            $table->enum('food_timing', ['before', 'after', 'any'])->nullable();
            $table->unsignedSmallInteger('days')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 30)->nullable()->unique();
            $table->string('category')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('lab_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique();
            $table->foreignId('patient_visit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('consultation_id')->nullable()->constrained('doctor_consultations')->nullOnDelete();
            $table->foreignId('lab_test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending')->index();
            $table->text('result_values')->nullable();
            $table->text('notes')->nullable();
            $table->string('report_path')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pharmacy_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique();
            $table->foreignId('patient_visit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prescription_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pharmacist_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending')->index();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pharmacy_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medicine_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('prescription_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('medicine_name');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('gst_percent', 5, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->boolean('is_given')->default(false);
            $table->boolean('is_otc')->default(false);
            $table->string('batch_number')->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 30)->unique();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_visit_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['opd', 'consultation', 'pharmacy', 'lab', 'final'])->default('opd');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('due_amount', 12, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->enum('method', ['cash', 'card', 'upi', 'online', 'other'])->default('cash');
            $table->string('transaction_ref')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('patient_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_visit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('consultation_id')->nullable()->constrained('doctor_consultations')->nullOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->enum('type', ['lab', 'xray', 'mri', 'ct', 'image', 'voice', 'other'])->default('other');
            $table->string('file_path');
            $table->boolean('is_private')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->enum('status', ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hospital_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('key')->index();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
            $table->unique(['branch_id', 'key']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['model_type', 'model_id']);
        });

        Schema::create('queue_displays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('current_token')->default(0);
            $table->unsignedInteger('waiting_count')->default(0);
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        $tables = [
            'queue_displays', 'audit_logs', 'hospital_settings', 'appointments',
            'patient_reports', 'payments', 'invoice_items', 'invoices',
            'pharmacy_order_items', 'pharmacy_orders', 'lab_orders', 'lab_tests',
            'medicine_batches', 'medicines', 'prescription_items', 'prescriptions',
            'doctor_consultations', 'patient_visits', 'patients', 'doctors',
            'departments', 'branches',
        ];
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'phone', 'employee_id', 'branch_id', 'department_id',
                'designation', 'signature_path', 'avatar', 'is_active', 'last_login_at',
            ]);
        });
    }
};
