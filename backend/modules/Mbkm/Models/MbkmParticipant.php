<?php
namespace Modules\Mbkm\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Student\Models\Student;
class MbkmParticipant extends Model { protected $fillable = ['application_id','student_id','program_id','participant_number','status','accepted_at','started_at','completed_at','notes']; protected function casts(): array { return ['accepted_at'=>'datetime','started_at'=>'datetime','completed_at'=>'datetime']; } public function application(): BelongsTo { return $this->belongsTo(MbkmApplication::class); } public function student(): BelongsTo { return $this->belongsTo(Student::class); } public function program(): BelongsTo { return $this->belongsTo(MbkmProgram::class); } public function activities(): HasMany { return $this->hasMany(MbkmActivity::class, 'participant_id'); } public function logbooks(): HasMany { return $this->hasMany(MbkmLogbook::class, 'participant_id'); } public function recognition(): HasOne { return $this->hasOne(MbkmRecognition::class, 'participant_id'); } }
