<?php
namespace Modules\Mbkm\Models;
use Illuminate\Database\Eloquent\Model;
class MbkmRecognitionCourse extends Model { protected $fillable = ['recognition_id','course_id','credits','score','letter_grade']; protected function casts(): array { return ['score'=>'decimal:2']; } }
