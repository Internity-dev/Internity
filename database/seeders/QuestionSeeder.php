<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Question;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $school = School::find(1);
        $questions = [
            [
                'question' => 'Bagaimana menurut Anda mutu pendidikan yang dilaksanakan di '.$school->name.'?',
                'order' => 1,
            ],
            [
                'question' => 'Bagaimana menurut Anda peralatan praktik di '.$school->name.'  sudah sesuai dengan standar perusahaan?',
                'order' => 2,
            ],
            [
                'question' => 'Bagaimana menurut Anda kemampuan akademik dan keterampilan siswa '.$school->name.' selama melaksanakan praktik kerja lapangan?',
                'order' => 3,
            ],
            [
                'question' => 'Bagaimana kesan/persepsi Anda terhadap program kerjasama hubungan industri '.$school->name.'?',
                'order' => 4,
            ],
            [
                'question' => 'Apakah tamatan/alumni '.$school->name.' menurut Anda sudah memenuhi kualifikasi perusahaan yang Anda pimpin?',
                'order' => 5,
            ],
            [
                'question' => 'Bagaimana pandangan Anda mengenai program Praktik Kerja Lapangan (PKL)'.$school->name.'?',
                'order' => 6,
            ],
            [
                'question' => 'Bagaimana menurut Anda lamanya waktu pelaksanaan Praktik Kerja Lapangan (PKL) minimal 3 (tiga) dan maksimal 6 (enam) bulan?',
                'order' => 7,
            ]
        ];

        foreach ($questions as $question) {
            Question::create([
                'question' => $question['question'],
                'school_id' => $school->id,
                'order' => $question['order'],
            ]);
        }
    }
}
