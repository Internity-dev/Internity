<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Faq::create([
            'question' => 'Tidak Bisa Login',
            'answer' => 'Banyak pengguna mengalami masalah ini karena salah mengakses website admin, bukan website client. Pastikan Anda mengakses website yang benar untuk login ke akun client.',
        ]);
    
        Faq::create([
            'question' => 'Tidak Bisa Absen',
            'answer' => 'Untuk absen, pastikan Anda sudah menentukan tanggal PKL di menu intern, tab "PKL-ku". Jika sudah melewati periode PKL, lakukan hal yang sama untuk menentukan tanggal sesuai periode PKL.',
        ]);
    }
}
