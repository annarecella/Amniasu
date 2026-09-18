<?php
namespace Modules\Mbkm\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class MbkmRecognition extends Model { protected $fillable = ['participant_id','semester_id','total_credits','final_score','letter_grade','status','approved_by','approved_at','locked_at','notes']; protected function casts(): array { return ['final_score'=>'decimal:2','approved_at'=>'datetime','locked_at'=>'datetime']; } public function courses(): HasMany { return $this->hasMany(MbkmRecognitionCourse::class, 'recognition_id'); } }
