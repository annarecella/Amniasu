<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mbkm_attendances', function (Blueprint $table) {
            $table->id(); $table->foreignId('participant_id')->constrained('mbkm_participants')->cascadeOnDelete();
            $table->date('attendance_date'); $table->string('status', 20); $table->decimal('hours', 5, 2)->nullable();
            $table->text('notes')->nullable(); $table->timestamps(); $table->unique(['participant_id', 'attendance_date']);
        });
        Schema::create('mbkm_assessment_components', function (Blueprint $table) {
            $table->id(); $table->foreignId('program_id')->constrained('mbkm_programs')->cascadeOnDelete();
            $table->string('name'); $table->decimal('weight', 5, 2); $table->decimal('maximum_score', 5, 2)->default(100); $table->boolean('is_required')->default(true); $table->timestamps();
        });
        Schema::create('mbkm_assessments', function (Blueprint $table) {
            $table->id(); $table->foreignId('participant_id')->constrained('mbkm_participants')->cascadeOnDelete();
            $table->foreignId('component_id')->constrained('mbkm_assessment_components')->cascadeOnDelete();
            $table->decimal('score', 5, 2); $table->foreignId('assessed_by')->nullable()->constrained('users')->nullOnDelete(); $table->text('notes')->nullable(); $table->timestamps();
            $table->unique(['participant_id', 'component_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('mbkm_assessments'); Schema::dropIfExists('mbkm_assessment_components'); Schema::dropIfExists('mbkm_attendances'); }
};
