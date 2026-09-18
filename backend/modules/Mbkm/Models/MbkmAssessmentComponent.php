<?php
namespace Modules\Mbkm\Models;
use Illuminate\Database\Eloquent\Model;
class MbkmAssessmentComponent extends Model { protected $fillable=['program_id','name','weight','maximum_score','is_required']; protected function casts(): array { return ['weight'=>'decimal:2','maximum_score'=>'decimal:2','is_required'=>'boolean']; } }
