<?php
namespace Modules\Mbkm\Models;
use Illuminate\Database\Eloquent\Model;
class MbkmActivity extends Model { protected $fillable = ['participant_id','title','description','planned_start','planned_end','status']; protected function casts(): array { return ['planned_start'=>'date','planned_end'=>'date']; } }
