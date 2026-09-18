<?php
namespace Modules\Mbkm\Models;
use Illuminate\Database\Eloquent\Model;
class MbkmAssessment extends Model { protected $fillable=['participant_id','component_id','score','assessed_by','notes']; protected function casts(): array { return ['score'=>'decimal:2']; } }
