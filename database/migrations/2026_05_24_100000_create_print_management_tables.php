<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paper_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->default('standard');
            $table->decimal('width_mm', 8, 2);
            $table->decimal('height_mm', 8, 2);
            $table->json('margins')->nullable();
            $table->string('orientation')->default('portrait');
            $table->decimal('scale', 5, 2)->default(100);
            $table->unsignedSmallInteger('dpi')->default(96);
            $table->json('padding')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('printer_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('pdf');
            $table->foreignId('paper_size_id')->nullable()->constrained()->nullOnDelete();
            $table->json('settings')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('hospital_branding', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('hospital_name')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('small_logo_path')->nullable();
            $table->string('watermark_path')->nullable();
            $table->text('hospital_address')->nullable();
            $table->string('hospital_phone')->nullable();
            $table->string('hospital_email')->nullable();
            $table->string('website')->nullable();
            $table->string('gst_number')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('license_number')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->text('footer_note')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->string('tagline')->nullable();
            $table->json('colors')->nullable();
            $table->json('header_config')->nullable();
            $table->json('footer_config')->nullable();
            $table->json('qr_settings')->nullable();
            $table->json('barcode_settings')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('font_library', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('family');
            $table->string('source')->default('google');
            $table->string('file_path')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('print_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('document_type');
            $table->foreignId('paper_size_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('printer_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('draft');
            $table->unsignedInteger('version')->default(1);
            $table->json('layout');
            $table->json('header')->nullable();
            $table->json('footer')->nullable();
            $table->json('theme')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_default')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['slug', 'branch_id']);
            $table->index(['document_type', 'status', 'is_default']);
        });

        Schema::create('print_template_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('print_template_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->json('layout');
            $table->json('header')->nullable();
            $table->json('footer')->nullable();
            $table->json('theme')->nullable();
            $table->json('settings')->nullable();
            $table->string('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('print_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
            $table->unique(['branch_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_settings');
        Schema::dropIfExists('print_template_versions');
        Schema::dropIfExists('print_templates');
        Schema::dropIfExists('font_library');
        Schema::dropIfExists('hospital_branding');
        Schema::dropIfExists('printer_profiles');
        Schema::dropIfExists('paper_sizes');
    }
};
