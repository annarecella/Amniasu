<?php

namespace Modules\Mbkm\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Mbkm\Models\MbkmProgramType;

class MbkmSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'student_exchange' => 'Pertukaran Mahasiswa', 'internship' => 'Magang / Praktik Kerja',
            'independent_study' => 'Studi Independen', 'teaching_campus' => 'Kampus Mengajar',
            'humanitarian_project' => 'Proyek Kemanusiaan', 'entrepreneurship' => 'Kegiatan Wirausaha',
            'research' => 'Penelitian / Riset', 'independent_project' => 'Proyek Independen',
            'village_project' => 'Membangun Desa / KKN Tematik', 'teaching_assistance' => 'Asistensi Mengajar',
            'international' => 'Program Internasional', 'institutional' => 'Program Mandiri Institusi', 'other' => 'Program MBKM Lainnya',
        ] as $code => $name) {
            MbkmProgramType::firstOrCreate(['code' => $code], ['name' => $name, 'is_active' => true]);
        }
    }
}
