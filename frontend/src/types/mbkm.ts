export interface MbkmProgramType { id: number; code: string; name: string }
export interface MbkmProgram { id: number; code: string; name: string; description?: string; status: string; registration_start: string; registration_end: string; implementation_start: string; implementation_end: string; credit_limit: number; quota?: number; participants_count?: number; type?: MbkmProgramType; partner?: { name: string } }
export interface MbkmApplication { id: number; application_number: string; status: string; submitted_at?: string; program: MbkmProgram }
