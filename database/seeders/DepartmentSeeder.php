<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $school = School::where('name', 'SMKN 1 Cibinong')->first();

        $departments = [
            [
                'name' => 'TKJ',
                'description' => 'Teknik Komputer dan Jaringan',
            ],
            [
                'name' => 'RPL',
                'description' => 'Rekayasa Perangkat Lunak',
            ],
            [
                'name' => 'DKV',
                'description' => 'Desain Komunikasi Visual',
            ],
            [
                'name' => 'SIJA',
                'description' => 'Sistem Informatika Jaringan dan Aplikasi',
            ],
            [
                'name' => 'TOI',
                'description' => 'Teknik Otomasi Industri',
            ],
            [
                'name' => 'TP',
                'description' => 'Teknik Pemesinan',
            ],
            [
                'name' => 'TFLM',
                'description' => 'Teknik Fabrikasi Logam dan Manufaktur'
            ],
            [
                'name' => 'TKR',
                'description' => 'Teknik Kendaraan Ringan',
            ],
            [
                'name' => 'DPIB',
                'description' => 'Desain Pemodelan Informasi Bangunan'
            ],
            [
                'name' => 'BKP',
                'description' => 'Bisnis Konstruksi Properti'
            ]
        ];

        foreach ($departments as $department) {
            Department::create([
                'name' => $department['name'],
                'description' => $department['description'],
                'school_id' => $school->id,
            ]);
        }

        // $school = School::where('name', 'SMKN 1 Bojonggede')->first();

        // $departments = [
        //     [
        //         'name' => 'AKL',
        //         'description' => 'Akuntansi dan Keuangan Lembaga',
        //     ],
        //     [
        //         'name' => 'MM',
        //         'description' => 'Multimedia',
        //     ],
        //     [
        //         'name' => 'PH',
        //         'description' => 'Perhotelan',
        //     ],
        //     [
        //         'name' => 'TB',
        //         'description' => 'Tata Boga',
        //     ],
        //     [
        //         'name' => 'UPW',
        //         'description' => 'Usaha Perjalanan Wisata',
        //     ]
        // ];

        // foreach ($departments as $department) {
        //     Department::create([
        //         'name' => $department['name'],
        //         'description' => $department['description'],
        //         'school_id' => $school->id,
        //     ]);
        // }

        // $school = School::where('name', 'SMKN 1 Depok')->first();

        // $departments = [
        //     [
        //         'name' => 'TBSM',
        //         'description' => 'Teknik Bisnis Sepeda Motor',
        //     ],
        //     [
        //         'name' => 'APH',
        //         'description' => 'Akomodasi Perhotelan',
        //     ],
        //     [
        //         'name' => 'TKRO',
        //         'description' => 'Teknik Kendaraan Ringan Otomotif',
        //     ],
        //     [
        //         'name' => 'AKL',
        //         'description' => 'Akuntansi dan Keuangan Lembaga',
        //     ],
        //     [
        //         'name' => 'MM',
        //         'description' => 'Multimedia',
        //     ],
        //     [
        //         'name' => 'RPL',
        //         'description' => 'Rekayasa Perangkat Lunak',
        //     ]
        // ];

        // foreach ($departments as $department) {
        //     Department::create([
        //         'name' => $department['name'],
        //         'description' => $department['description'],
        //         'school_id' => $school->id,
        //     ]);
        // }
    }
}
