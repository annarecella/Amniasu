<?php

namespace Modules\Mbkm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Academic\Models\Semester;
use Modules\Audit\Traits\Auditable;

class MbkmProgram extends Model
{
    use Auditable;

    protected $fillable = ['code', 'name', 'program_type_id', 'semester_id', 'partner_id', 'organizer_type', 'organizer_id', 'description', 'registration_start', 'registration_end', 'implementation_start', 'implementation_end', 'credit_limit', 'quota', 'status', 'delivery_mode', 'country', 'province', 'city', 'address', 'latitude', 'longitude', 'created_by', 'updated_by'];
    protected function casts(): array { return ['registration_start' => 'date', 'registration_end' => 'date', 'implementation_start' => 'date', 'implementation_end' => 'date', 'latitude' => 'float', 'longitude' => 'float']; }
    public function semester(): BelongsTo { return $this->belongsTo(Semester::class); }
    public function type(): BelongsTo { return $this->belongsTo(MbkmProgramType::class, 'program_type_id'); }
    public function partner(): BelongsTo { return $this->belongsTo(MbkmPartner::class); }
    public function targets(): HasMany { return $this->hasMany(MbkmProgramTarget::class, 'program_id'); }
    public function requirements(): HasMany { return $this->hasMany(MbkmProgramRequirement::class, 'program_id')->orderBy('sort_order'); }
    public function applications(): HasMany { return $this->hasMany(MbkmApplication::class, 'program_id'); }
    public function participants(): HasMany { return $this->hasMany(MbkmParticipant::class, 'program_id'); }
    public function acceptsApplications(): bool { return $this->status === 'registration_open' && now()->between($this->registration_start->startOfDay(), $this->registration_end->endOfDay()); }
}
