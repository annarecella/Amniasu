<?php
namespace Modules\Mbkm\Models;
use Illuminate\Database\Eloquent\Model;
class MbkmAttendance extends Model { protected $fillable=['participant_id','attendance_date','status','hours','notes']; protected function casts(): array { return ['attendance_date'=>'date','hours'=>'decimal:2']; } }
