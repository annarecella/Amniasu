<?php

namespace Modules\Mbkm\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Mbkm\Models\MbkmApplication;
use Modules\Mbkm\Models\MbkmParticipant;
use Modules\Mbkm\Models\MbkmProgram;
use Modules\Mbkm\Services\MbkmEligibilityService;

class MbkmApplicationController extends Controller
{
    use HasApiResponse;
    public function __construct(private MbkmEligibilityService $eligibility) {}

    public function index(Request $request): JsonResponse
    {
        $query = MbkmApplication::with(['student.studyProgram','program.type','documents']);
        if ($request->user()->hasRole('mahasiswa')) $query->where('student_id', $request->user()->student?->id ?? 0);
        if ($request->filled('status')) $query->where('status', $request->status);
        return $this->paginatedResponse($query->latest()->paginate((int) $request->query('per_page', 15)), 'Daftar pendaftaran MBKM berhasil dimuat.');
    }
    public function store(Request $request): JsonResponse
    {
        $student = $request->user()->student;
        if (!$student) return $this->errorResponse('Profil mahasiswa tidak ditemukan.', 404);
        $data = $request->validate(['program_id'=>['required','exists:mbkm_programs,id'], 'motivation'=>['nullable','string','max:5000'], 'notes'=>['nullable','string']]);
        $program = MbkmProgram::with(['targets','requirements'])->findOrFail($data['program_id']);
        $this->eligibility->validate($student, $program);
        $application = MbkmApplication::firstOrCreate(['student_id'=>$student->id, 'program_id'=>$program->id], ['application_number'=>'MBKM-'.now()->format('Ymd').'-'.str_pad((string)(MbkmApplication::count()+1), 5, '0', STR_PAD_LEFT), 'motivation'=>$data['motivation'] ?? null, 'notes'=>$data['notes'] ?? null]);
        return $this->successResponse($application->load(['program.type','documents']), 'Draft pendaftaran MBKM berhasil dibuat.', 201);
    }
    public function submit(Request $request, MbkmApplication $mbkmApplication): JsonResponse
    {
        $this->ensureOwner($request, $mbkmApplication);
        if ($mbkmApplication->status !== 'draft') throw ValidationException::withMessages(['status'=>['Hanya draft yang dapat diajukan.']]);
        $program = $mbkmApplication->program->load(['targets','requirements']);
        $this->eligibility->validate($mbkmApplication->student, $program);
        $required = $program->requirements->where('category','document')->where('is_required',true)->pluck('id');
        if ($required->diff($mbkmApplication->documents()->pluck('requirement_id'))->isNotEmpty()) throw ValidationException::withMessages(['documents'=>['Dokumen wajib belum lengkap.']]);
        $mbkmApplication->update(['status'=>'submitted','submitted_at'=>now()]);
        return $this->successResponse($mbkmApplication->fresh(), 'Pendaftaran MBKM berhasil diajukan.');
    }
    public function review(Request $request, MbkmApplication $mbkmApplication): JsonResponse
    {
        $data=$request->validate(['status'=>['required','in:under_review,eligible,ineligible,selected,rejected'], 'review_notes'=>['nullable','string']]);
        DB::transaction(function () use ($request,$mbkmApplication,$data) {
            $mbkmApplication->update($data+['reviewed_by'=>$request->user()->id,'reviewed_at'=>now()]);
            if ($data['status'] === 'selected') MbkmParticipant::firstOrCreate(['application_id'=>$mbkmApplication->id], ['student_id'=>$mbkmApplication->student_id,'program_id'=>$mbkmApplication->program_id,'participant_number'=>'MBKM-P-'.now()->format('Ymd').'-'.str_pad((string)(MbkmParticipant::count()+1),5,'0',STR_PAD_LEFT),'accepted_at'=>now()]);
        });
        return $this->successResponse($mbkmApplication->fresh(), 'Hasil seleksi MBKM berhasil disimpan.');
    }
    private function ensureOwner(Request $request, MbkmApplication $application): void { if (!$request->user()->hasRole('mahasiswa') || $request->user()->student?->id !== $application->student_id) abort(403); }
}
