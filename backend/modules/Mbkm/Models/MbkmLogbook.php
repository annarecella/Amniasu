<?php
namespace Modules\Mbkm\Models;
use Illuminate\Database\Eloquent\Model;
class MbkmLogbook extends Model { protected $fillable = ['participant_id','activity_id','activity_date','description','hours','status','reviewed_by','reviewed_at','review_notes']; protected function casts(): array { return ['activity_date'=>'date','hours'=>'decimal:2','reviewed_at'=>'datetime']; } }
