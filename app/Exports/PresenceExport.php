<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Models\Presence;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PresenceExport implements FromCollection, WithHeadings, WithTitle
{
    protected $userId;
    protected $companyId;

    public function __construct(Request $request)
    {
        $this->userId = decrypt($request->query('user'));
        $this->companyId = decrypt($request->query('company'));
    }

    public function collection()
    {
        $userId = $this->userId;
        $companyId = $this->companyId;
        
        // Fetch the presences based on the user_id and company_id
        $presences = Presence::where('user_id', $userId)
                             ->where('company_id', $companyId)
                             ->orderBy('date', 'asc')
                             ->get();

        return $presences->map(function ($presence) {
            return [
                'Tanggal' => $presence->date,
                'Jam Masuk' => $presence->check_in,
                'Jam Keluar' => $presence->check_out,
                'Status' => $presence->is_approved ? 'Sudah Disetujui' : 'Belum Disetujui',
            ];
        });
    }

    public function headings(): array
    {
        return [
            ['Nama', 'Kelas', 'Company'],
            ['', '', ''],
            ['Tanggal', 'Jam Masuk', 'Jam Keluar', 'Status']
        ];
    }

    public function title(): string
    {
        return 'Presences Data';
    }

    public function startCell(): string
    {
        return 'A3';
    }

    public function styles(Worksheet $sheet)
    {
        $user = User::find($this->userId);

        $sheet->setCellValue('A1', 'Nama: ' . $user->name);
        $sheet->setCellValue('B1', 'Kelas: ' . ($user->courses()->first()?->name ?? 'N/A'));
        $sheet->setCellValue('C1', 'Company: ' . $user->companies()->find($this->companyId)->name);

        // Atur style untuk baris pertama
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        $sheet->getStyle('A3:D3')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFD9D9D9',
                ],
            ],
        ]);
    }
}
