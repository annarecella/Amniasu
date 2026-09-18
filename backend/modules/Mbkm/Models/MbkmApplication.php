<?php
namespace Modules\Mbkm\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Audit\Traits\Auditable;
use Modules\Student\Models\Student;
class MbkmApplication extends Model { use Auditable; protected $fillable = ['student_id','program_id','application_number','motivation','notes','status','submitted_at','reviewed_by','reviewed_at','review_notes']; protected function casts(): array { return ['submitted_at'=>'datetime','reviewed_at'=>'datetime']; } public function student(): BelongsTo { return $this->belongsTo(Student::class); } public function program(): BelongsTo { return $this->belongsTo(MbkmProgram::class); } public function documents(): HasMany { return $this->hasMany(MbkmApplicationDocument::class, 'application_id'); } }
