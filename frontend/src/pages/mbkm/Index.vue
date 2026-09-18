<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import PageContainer from '@/components/data-display/PageContainer.vue'
import { mbkmService } from '@/services/api/mbkm'
import type { MbkmApplication, MbkmProgram } from '@/types/mbkm'

const auth = useAuthStore()
const programs = ref<MbkmProgram[]>([])
const applications = ref<MbkmApplication[]>([])
const loading = ref(true)
const isStudent = computed(() => auth.user?.roles?.some(role => role.name === 'mahasiswa'))

async function load() {
  loading.value = true
  try {
    const [programResponse, applicationResponse] = await Promise.all([mbkmService.programs({ status: 'registration_open' }), mbkmService.applications()])
    programs.value = programResponse.data
    applications.value = applicationResponse.data
  } finally { loading.value = false }
}

onMounted(load)
</script>

<template>
  <PageContainer>
    <div class="mb-6">
      <p class="text-xs font-medium text-brand-600">AKADEMIK</p>
      <h1 class="text-2xl font-bold text-slate-900">MBKM</h1>
      <p class="mt-1 text-sm text-slate-500">Program Merdeka Belajar Kampus Merdeka, pendaftaran, dan progres rekognisi.</p>
    </div>
    <div v-if="loading" class="rounded-xl border border-slate-200 bg-white p-8 text-sm text-slate-500">Memuat data MBKM…</div>
    <template v-else>
      <section class="mb-8">
        <div class="mb-3 flex items-center justify-between"><h2 class="font-semibold text-slate-800">Program yang membuka pendaftaran</h2><span class="text-sm text-slate-500">{{ programs.length }} program</span></div>
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
          <article v-for="program in programs" :key="program.id" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3"><div><p class="text-xs font-medium text-brand-600">{{ program.code }}</p><h3 class="mt-1 font-semibold text-slate-900">{{ program.name }}</h3></div><span class="rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">Pendaftaran</span></div>
            <p class="mt-3 text-sm text-slate-500">{{ program.type?.name }} <span v-if="program.partner">· {{ program.partner.name }}</span></p>
            <div class="mt-4 border-t pt-3 text-xs text-slate-500">{{ program.registration_start }} — {{ program.registration_end }} · Maks. {{ program.credit_limit }} SKS</div>
          </article>
          <div v-if="!programs.length" class="rounded-xl border border-dashed border-slate-300 p-5 text-sm text-slate-500">Belum ada program MBKM yang membuka pendaftaran.</div>
        </div>
      </section>
      <section v-if="isStudent">
        <h2 class="mb-3 font-semibold text-slate-800">Pendaftaran saya</h2>
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white"><table class="w-full text-left text-sm"><thead class="bg-slate-50 text-xs text-slate-500"><tr><th class="px-4 py-3">Nomor</th><th class="px-4 py-3">Program</th><th class="px-4 py-3">Status</th></tr></thead><tbody><tr v-for="application in applications" :key="application.id" class="border-t"><td class="px-4 py-3 font-medium">{{ application.application_number }}</td><td class="px-4 py-3">{{ application.program?.name }}</td><td class="px-4 py-3 capitalize">{{ application.status }}</td></tr><tr v-if="!applications.length"><td colspan="3" class="px-4 py-5 text-center text-slate-500">Belum ada pendaftaran MBKM.</td></tr></tbody></table></div>
      </section>
    </template>
  </PageContainer>
</template>
