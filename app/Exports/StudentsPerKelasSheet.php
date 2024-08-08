<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

class StudentsPerKelasSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $kelas;
    protected $students;

    public function __construct($kelas, Collection $students)
    {
        $this->kelas = $kelas;
        $this->students = $students;
    }

    public function collection()
    {
        return $this->students->map(function ($user) {
            return [
                'nama' => $user->name,
                'kelas' => $user->courses()->first()?->name ?? 'N/A', // Adjust based on your actual data
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Kelas'
        ];
    }

    public function title(): string
    {
        return $this->kelas;
    }
}
