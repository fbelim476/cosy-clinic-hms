<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('token_prefix', 10)->nullable()->after('department_id');
            $table->string('qualification')->nullable()->after('specialization');
            $table->string('room_number', 20)->nullable()->after('consultation_fee');
            $table->unsignedSmallInteger('daily_queue_limit')->nullable()->after('room_number');
            $table->string('profile_photo_path')->nullable()->after('daily_queue_limit');
        });

        Schema::table('patient_visits', function (Blueprint $table) {
            $table->string('token_code', 20)->nullable()->after('token_number');
            $table->index(['doctor_id', 'created_at']);
        });

        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('slot_duration_minutes')->default(15);
            $table->unsignedSmallInteger('max_patients')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['doctor_id', 'day_of_week']);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('doctor_id')->constrained()->nullOnDelete();
            $table->string('time_slot', 20)->nullable()->after('appointment_time');
            $table->foreignId('patient_visit_id')->nullable()->after('notes')->constrained('patient_visits')->nullOnDelete();
            $table->timestamp('checked_in_at')->nullable()->after('patient_visit_id');
        });

        Schema::table('queue_displays', function (Blueprint $table) {
            $table->foreignId('doctor_id')->nullable()->after('department_id')->constrained()->nullOnDelete();
            $table->string('current_token_code', 20)->nullable()->after('current_token');
        });
    }

    public function down(): void
    {
        Schema::table('queue_displays', function (Blueprint $table) {
            $table->dropConstrainedForeignId('doctor_id');
            $table->dropColumn('current_token_code');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->dropConstrainedForeignId('patient_visit_id');
            $table->dropColumn(['time_slot', 'checked_in_at']);
        });

        Schema::dropIfExists('doctor_schedules');

        Schema::table('patient_visits', function (Blueprint $table) {
            $table->dropIndex(['doctor_id', 'created_at']);
            $table->dropColumn('token_code');
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
            $table->dropColumn([
                'token_prefix', 'qualification', 'room_number',
                'daily_queue_limit', 'profile_photo_path',
            ]);
        });
    }
};
