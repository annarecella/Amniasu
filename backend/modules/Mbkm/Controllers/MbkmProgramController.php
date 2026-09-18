<?php

namespace Modules\Mbkm\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Mbkm\Models\MbkmPartner;
use Modules\Mbkm\Models\MbkmProgram;
use Modules\Mbkm\Models\MbkmProgramType;

class MbkmProgramController extends Controller
{
    use HasApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = MbkmProgram::with(['type', 'partner', 'semester.academicYear', 'targets', 'requirements'])->withCount('participants');
        foreach (['status', 'semester_id', 'program_type_id', 'partner_id'] as $filter) if ($request->filled($filter)) $query->where($filter, $request->query($filter));
        if ($request->filled('search')) $query->where(fn ($q) => $q->where('code', 'like', '%'.$request->search.'%')->orWhere('name', 'like', '%'.$request->search.'%'));
        return $this->paginatedResponse($query->latest()->paginate((int) $request->query('per_page', 15)), 'Daftar program MBKM berhasil dimuat.');
    }

    public function store(Request $request): JsonResponse { return $this->persist($request, new MbkmProgram(), 201); }
    public function show(MbkmProgram $mbkmProgram): JsonResponse { return $this->successResponse($mbkmProgram->load(['type','partner','semester.academicYear','targets.studyProgram','requirements','applications.student','participants.student']), 'Detail program MBKM berhasil dimuat.'); }
    public function update(Request $request, MbkmProgram $mbkmProgram): JsonResponse { return $this->persist($request, $mbkmProgram); }

    public function types(): JsonResponse { return $this->successResponse(MbkmProgramType::where('is_active', true)->orderBy('name')->get(), 'Jenis program MBKM berhasil dimuat.'); }
    public function partners(): JsonResponse { return $this->successResponse(MbkmPartner::where('status', 'active')->orderBy('name')->get(), 'Mitra MBKM berhasil dimuat.'); }

    private function persist(Request $request, MbkmProgram $program, int $status = 200): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required','string','max:50','unique:mbkm_programs,code,'.$program->id], 'name' => ['required','string','max:255'],
            'program_type_id' => ['required','exists:mbkm_program_types,id'], 'semester_id' => ['required','exists:semesters,id'], 'partner_id' => ['nullable','exists:mbkm_partners,id'],
            'organizer_type' => ['required','in:institution,faculty,study_program,academic_unit,external_partner'], 'organizer_id' => ['nullable','integer'],
            'description' => ['nullable','string'], 'registration_start' => ['required','date'], 'registration_end' => ['required','date','after_or_equal:registration_start'],
            'implementation_start' => ['required','date'], 'implementation_end' => ['required','date','after_or_equal:implementation_start'], 'credit_limit' => ['required','integer','min:1','max:40'], 'quota' => ['nullable','integer','min:1'],
            'status' => ['required','in:draft,published,registration_open,registration_closed,selection,ongoing,completed,cancelled,archived'], 'delivery_mode' => ['required','in:on_campus,off_campus,domestic,international,remote,hybrid,offline'],
            'country'=>['nullable','string'],'province'=>['nullable','string'],'city'=>['nullable','string'],'address'=>['nullable','string'],'latitude'=>['nullable','numeric'],'longitude'=>['nullable','numeric'],
            'targets'=>['array'], 'targets.*.study_program_id'=>['nullable','exists:study_programs,id'], 'targets.*.minimum_semester'=>['nullable','integer','min:1'], 'targets.*.maximum_semester'=>['nullable','integer','min:1'], 'targets.*.minimum_gpa'=>['nullable','numeric','between:0,4'], 'targets.*.minimum_credits'=>['nullable','integer','min:0'],
            'requirements'=>['array'], 'requirements.*.category'=>['required','in:academic,document,administrative,custom'], 'requirements.*.name'=>['required','string'], 'requirements.*.description'=>['nullable','string'], 'requirements.*.rules'=>['nullable','array'], 'requirements.*.is_required'=>['boolean'],
        ]);
        DB::transaction(function () use ($request, &$program, $data) {
            $program->fill(collect($data)->except(['targets','requirements'])->all());
            $program->exists ? $program->updated_by = $request->user()->id : $program->created_by = $request->user()->id;
            $program->save();
            if (array_key_exists('targets', $data)) { $program->targets()->delete(); $program->targets()->createMany($data['targets']); }
            if (array_key_exists('requirements', $data)) { $program->requirements()->delete(); $program->requirements()->createMany($data['requirements']); }
        });
        return $this->successResponse($program->fresh()->load(['type','partner','targets','requirements']), 'Program MBKM berhasil disimpan.', $status);
    }
}
