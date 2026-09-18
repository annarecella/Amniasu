<?php

namespace Modules\Mbkm\Services;

use Illuminate\Validation\ValidationException;
use Modules\Enrollment\Models\StudentEnrollment;
use Modules\Mbkm\Models\MbkmProgram;
use Modules\Student\Enums\StudentStatus;
use Modules\Student\Models\Student;

class MbkmEligibilityService
{
    /** Validate program windows, quota, target criteria, and required documents. */
    public function validate(Student $student, MbkmProgram $program, bool $requireDocuments = false): void
    {
        $errors = [];
        if ($student->status !== StudentStatus::ACTIVE) $errors['student_id'][] = 'Mahasiswa harus berstatus aktif.';
        if (!$program->acceptsApplications()) $errors['program_id'][] = 'Program tidak sedang membuka pendaftaran.';
        if ($program->quota !== null && $program->participants()->count() >= $program->quota) $errors['program_id'][] = 'Kuota program telah terpenuhi.';

        $semesterLevel = (int) ceil(max(1, now()->year - (int) $student->admission_year + 1) * 2);
        $completedCredits = (int) StudentEnrollment::where('student_id', $student->id)->sum('total_credits');
        foreach ($program->targets as $target) {
            if ($target->study_program_id && $target->study_program_id !== $student->study_program_id) continue;
            if ($target->admission_year && $target->admission_year !== $student->admission_year) continue;
            if ($target->student_status !== $student->status->value) continue;
            if ($target->minimum_semester && $semesterLevel < $target->minimum_semester) continue;
            if ($target->maximum_semester && $semesterLevel > $target->maximum_semester) continue;
            if ($target->minimum_credits && $completedCredits < $target->minimum_credits) continue;
            return;
        }
        if ($program->targets->isNotEmpty()) $errors['program_id'][] = 'Mahasiswa tidak memenuhi target peserta program.';
        if ($requireDocuments) {
            $documentRequirements = $program->requirements->where('category', 'document')->where('is_required', true);
            if ($documentRequirements->isNotEmpty()) $errors['documents'][] = 'Seluruh dokumen wajib harus diunggah sebelum pengajuan.';
        }
        if ($errors) throw ValidationException::withMessages($errors);
    }
}
