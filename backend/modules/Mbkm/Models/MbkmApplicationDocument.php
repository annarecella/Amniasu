<?php
namespace Modules\Mbkm\Models;
use Illuminate\Database\Eloquent\Model;
class MbkmApplicationDocument extends Model { protected $fillable = ['application_id','requirement_id','name','file_path','status','verified_by','verified_at','notes']; protected function casts(): array { return ['verified_at'=>'datetime']; } }
