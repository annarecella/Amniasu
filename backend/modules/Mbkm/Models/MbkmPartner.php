<?php
namespace Modules\Mbkm\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Audit\Traits\Auditable;
class MbkmPartner extends Model { use Auditable; protected $fillable = ['name','partner_type','institution_type','legal_entity','address','city','province','country','website','email','phone','contact_person','contact_position','status','notes']; public function programs(): HasMany { return $this->hasMany(MbkmProgram::class, 'partner_id'); } }
