<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mbkm_program_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('mbkm_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('partner_type', 50);
            $table->string('institution_type', 100)->nullable();
            $table->string('legal_entity')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('country')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_position')->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('mbkm_partnership_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('mbkm_partners')->cascadeOnDelete();
            $table->string('document_number')->nullable();
            $table->string('document_type', 30);
            $table->date('signed_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('file_path')->nullable();
            $table->string('status', 30)->default('active');
            $table->timestamps();
        });

        Schema::create('mbkm_programs', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->foreignId('program_type_id')->constrained('mbkm_program_types')->restrictOnDelete();
            $table->foreignId('semester_id')->constrained()->restrictOnDelete();
            $table->foreignId('partner_id')->nullable()->constrained('mbkm_partners')->nullOnDelete();
            $table->string('organizer_type', 30)->default('study_program');
            $table->unsignedBigInteger('organizer_id')->nullable();
            $table->text('description')->nullable();
            $table->date('registration_start');
            $table->date('registration_end');
            $table->date('implementation_start');
            $table->date('implementation_end');
            $table->unsignedTinyInteger('credit_limit')->default(20);
            $table->unsignedInteger('quota')->nullable();
            $table->string('status', 30)->default('draft')->index();
            $table->string('delivery_mode', 30)->default('offline');
            $table->string('country')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('mbkm_program_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('mbkm_programs')->cascadeOnDelete();
            $table->foreignId('study_program_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('degree_level', 30)->nullable();
            $table->unsignedTinyInteger('minimum_semester')->nullable();
            $table->unsignedTinyInteger('maximum_semester')->nullable();
            $table->unsignedSmallInteger('admission_year')->nullable();
            $table->decimal('minimum_gpa', 3, 2)->nullable();
            $table->unsignedTinyInteger('minimum_credits')->nullable();
            $table->string('student_status', 30)->default('active');
            $table->timestamps();
        });

        Schema::create('mbkm_program_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('mbkm_programs')->cascadeOnDelete();
            $table->string('category', 30);
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('rules')->nullable();
            $table->boolean('is_required')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('mbkm_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('mbkm_programs')->cascadeOnDelete();
            $table->string('application_number', 50)->unique();
            $table->text('motivation')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('draft')->index();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'program_id']);
        });

        Schema::create('mbkm_application_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('mbkm_applications')->cascadeOnDelete();
            $table->foreignId('requirement_id')->nullable()->constrained('mbkm_program_requirements')->nullOnDelete();
            $table->string('name');
            $table->string('file_path');
            $table->string('status', 30)->default('uploaded');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('mbkm_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('mbkm_applications')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('mbkm_programs')->cascadeOnDelete();
            $table->string('participant_number', 50)->unique();
            $table->string('status', 30)->default('accepted')->index();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'program_id']);
        });

        Schema::create('mbkm_supervisors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('mbkm_participants')->cascadeOnDelete();
            $table->foreignId('lecturer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('external_name')->nullable();
            $table->string('external_email')->nullable();
            $table->string('role', 30)->default('academic');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('mbkm_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('mbkm_participants')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('planned_start')->nullable();
            $table->date('planned_end')->nullable();
            $table->string('status', 30)->default('planned');
            $table->timestamps();
        });

        Schema::create('mbkm_logbooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('mbkm_participants')->cascadeOnDelete();
            $table->foreignId('activity_id')->nullable()->constrained('mbkm_activities')->nullOnDelete();
            $table->date('activity_date');
            $table->text('description');
            $table->decimal('hours', 5, 2)->nullable();
            $table->string('status', 30)->default('submitted');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('mbkm_recognitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->unique()->constrained('mbkm_participants')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('total_credits')->default(0);
            $table->decimal('final_score', 5, 2)->nullable();
            $table->string('letter_grade', 5)->nullable();
            $table->string('status', 30)->default('draft')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('mbkm_recognition_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recognition_id')->constrained('mbkm_recognitions')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('credits');
            $table->decimal('score', 5, 2)->nullable();
            $table->string('letter_grade', 5)->nullable();
            $table->timestamps();
            $table->unique(['recognition_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mbkm_recognition_courses'); Schema::dropIfExists('mbkm_recognitions');
        Schema::dropIfExists('mbkm_logbooks'); Schema::dropIfExists('mbkm_activities');
        Schema::dropIfExists('mbkm_supervisors'); Schema::dropIfExists('mbkm_participants');
        Schema::dropIfExists('mbkm_application_documents'); Schema::dropIfExists('mbkm_applications');
        Schema::dropIfExists('mbkm_program_requirements'); Schema::dropIfExists('mbkm_program_targets');
        Schema::dropIfExists('mbkm_programs'); Schema::dropIfExists('mbkm_partnership_documents');
        Schema::dropIfExists('mbkm_partners'); Schema::dropIfExists('mbkm_program_types');
    }
};
