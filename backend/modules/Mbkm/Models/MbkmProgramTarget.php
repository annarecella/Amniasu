<?php
namespace Modules\Mbkm\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Academic\Models\StudyProgram;
class MbkmProgramTarget extends Model { protected $fillable = ['program_id','study_program_id','degree_level','minimum_semester','maximum_semester','admission_year','minimum_gpa','minimum_credits','student_status']; protected function casts(): array { return ['minimum_gpa'=>'decimal:2']; } public function studyProgram(): BelongsTo { return $this->belongsTo(StudyProgram::class); } }
