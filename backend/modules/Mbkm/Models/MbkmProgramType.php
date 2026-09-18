<?php
namespace Modules\Mbkm\Models;
use Illuminate\Database\Eloquent\Model;
class MbkmProgramType extends Model { protected $fillable = ['code', 'name', 'description', 'is_active']; protected function casts(): array { return ['is_active' => 'boolean']; } }
