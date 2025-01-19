<?php

namespace App\Http\Controllers\Api;

// use ZipArchive;
use mikehaertl\pdftk\Pdf;
use Carbon\Carbon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function exportJournal(Request $request)
    {
        $user = auth()->user();
        $courseName = $user->courses->first()?->name;
        $courseLevel = $courseName ? explode(' ', $courseName)[0] : 'N/A';

        $formattedDateOfBirth = $user->date_of_birth
        ? Carbon::parse($user->date_of_birth)->translatedFormat('j F Y')
        : 'N/A';

        $gender = match ($user->gender) {
            'male' => 'Laki-Laki',
            'female' => 'Perempuan'
        };

        $companyId = $request->query('company', $user->companies->first()->id);

        $company = $user->companies()->find($companyId);
        $companyName = $company?->name ?? 'N/A';

        $presences = $user->presences()
            ->where('company_id', $companyId)
            ->whereDate('date', '<=', Carbon::now())
            ->orderBy('date', 'asc')
            ->get()
            ->filter(fn($presence) => !is_null($presence->check_in))
            ->values();

        $journals = $user->journals()
            ->where('company_id', $companyId)
            ->whereDate('date', '<=', Carbon::now())
            ->orderBy('date', 'asc')
            ->get()
            ->filter(fn($journal) => !is_null($journal->work_type))
            ->values();

        $formFields = [
            'namasiswa' => $user->name,
            'kelas' => $courseLevel,
            'alamat_siswa' => $user->address,
            'jurusan' => $user->departments->first()?->description,
            'instansi_nama' => $companyName,
            'kelasjurusan' => $courseName,
            'ttl' => $formattedDateOfBirth,
            'gender' => $gender,
        ];

        foreach ($presences as $index => $presence) {
            $i = $index + 1;
            $formFields["tanggal$i"] = Carbon::parse($presence->date)->translatedFormat('l, j F Y');
            $formFields["checkin$i"] = $presence->check_in;
            $formFields["checkout$i"] = $presence->check_out ?? 'N/A';
        }

        foreach ($journals as $index => $journal) {
            $i = $index + 1;
            $formFields["no$i"] = $i;
            $formFields["bidang$i"] = $journal->work_type;
            $formFields["uraian$i"] = $journal->description;
            $formFields["tgl_jurnal$i"] = Carbon::parse($journal->date)->translatedFormat('l, j F Y');
        }

        $templatePath = '';
        if (count($presences) <= 93 || count($journals) <= 93) {
            $templatePath = public_path('template-pdf/template_3_month.pdf');
        } else {
            $templatePath = public_path('template-pdf/template_6_month.pdf');
        }

        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'PDF template not found!'], 404);
        }

        $fileName = $user->id . time() . '.pdf';
        $tempPath = storage_path('storage/journals/' . $fileName);
        $outputPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName;
        $pdf = new Pdf($templatePath);
        $pdf->fillForm($formFields)->flatten()->saveAs($outputPath);

        return response()->download($outputPath, $fileName)->deleteFileAfterSend(true);
    }
}
