import { apiClient } from './client'
import type { ApiResponse } from '@/types/api'
import type { MbkmApplication, MbkmProgram, MbkmProgramType } from '@/types/mbkm'

export const mbkmService = {
  programs(params?: Record<string, unknown>): Promise<ApiResponse<MbkmProgram[]>> { return apiClient.get('/mbkm/programs', params) },
  programTypes(): Promise<ApiResponse<MbkmProgramType[]>> { return apiClient.get('/mbkm/program-types') },
  applications(params?: Record<string, unknown>): Promise<ApiResponse<MbkmApplication[]>> { return apiClient.get('/mbkm/applications', params) },
  apply(program_id: number, motivation?: string): Promise<ApiResponse<MbkmApplication>> { return apiClient.post('/mbkm/applications', { program_id, motivation }) },
  submit(id: number): Promise<ApiResponse<MbkmApplication>> { return apiClient.post(`/mbkm/applications/${id}/submit`) },
}
