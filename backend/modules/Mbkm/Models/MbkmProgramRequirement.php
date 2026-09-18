<?php
namespace Modules\Mbkm\Models;
use Illuminate\Database\Eloquent\Model;
class MbkmProgramRequirement extends Model { protected $fillable = ['program_id','category','name','description','rules','is_required','sort_order']; protected function casts(): array { return ['rules'=>'array','is_required'=>'boolean']; } }
