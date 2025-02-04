<?php

namespace App\Http\Controllers\Api;

// use ZipArchive;
use mikehaertl\pdftk\Pdf;
use Carbon\Carbon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function pdfSingleCompany(Request $request, $id)
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

        $companyId = $id;
        $company = $user->companies()->find($companyId);
        if (!$company) {
            return response()->json(['error' => 'Company not found!'], 404);
        }

        $internDate = $user->internDates()->where('company_id', $companyId)->first();

        $startDate = $internDate ? Carbon::parse($internDate->start_date)->translatedFormat('j F Y') : 'N/A';
        $endDate = $internDate ? Carbon::parse($internDate->end_date)->translatedFormat('j F Y') : 'N/A';
    
        $presences = $user->presences()
            ->where('company_id', $companyId)
            ->whereDate('date', '<=', Carbon::now())
            ->orderBy('date', 'asc')
            ->get()
            ->filter(fn($presence) => !is_null($presence->check_in))
            ->values();

        if ($presences->isEmpty()) {
            return response()->json(['error' => 'No presences found!'], 404);
        }

        $journals = $user->journals()
            ->where('company_id', $companyId)
            ->whereDate('date', '<=', Carbon::now())
            ->orderBy('date', 'asc')
            ->get()
            ->filter(fn($journal) => !is_null($journal->work_type))
            ->values();
        
        if ($journals->isEmpty()) {
            return response()->json(['error' => 'No journals found!'], 404);
        }

        $formFields = [
            'namasiswa' => $user->name,
            'kelas' => $courseLevel,
            'alamat_siswa' => $user->address,
            'jurusan' => $user->departments->first()?->description,
            'kelasjurusan' => $courseName,
            'ttl' => $formattedDateOfBirth,
            'gender' => $gender,
            'tgl_export' => Carbon::now()->translatedFormat('l, j F Y'),
            'instansi_nama' => $company->name,
            'instansi_bidang' => $company->category,
            'instansi_alamat' => $company->address,
            'instansi_hrd' => $company->contact_person,
            'instansi_tglmulai' => $startDate,
            'instansi_tglselesai' => $endDate,
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

    public function pdfMultipleCompany(Request $request)
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
    
        $companies = $user->companies()->take(2)->get();
    
        if ($companies->count() < 2) {
            return response()->json(['error' => 'Insufficient companies associated with the user!'], 404);
        }
    
        $company1 = $companies[0];
        $company2 = $companies[1];
    
        $presences1 = $user->presences()
            ->where('company_id', $company1->id)
            ->whereDate('date', '<=', Carbon::now())
            ->orderBy('date', 'asc')
            ->get()
            ->filter(fn($presence) => !is_null($presence->check_in))
            ->values();
    
        $journals1 = $user->journals()
            ->where('company_id', $company1->id)
            ->whereDate('date', '<=', Carbon::now())
            ->orderBy('date', 'asc')
            ->get()
            ->filter(fn($journal) => !is_null($journal->work_type))
            ->values();
    
        $presences2 = $user->presences()
            ->where('company_id', $company2->id)
            ->whereDate('date', '<=', Carbon::now())
            ->orderBy('date', 'asc')
            ->get()
            ->filter(fn($presence) => !is_null($presence->check_in))
            ->values();
    
        $journals2 = $user->journals()
            ->where('company_id', $company2->id)
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
            'kelasjurusan' => $courseName,
            'ttl' => $formattedDateOfBirth,
            'gender' => $gender,
            'tgl_export' => Carbon::now()->translatedFormat('l, j F Y'),
            'instansi1_nama' => $company1->name,
            'instansi1_bidang' => $company1->category,
            'instansi1_alamat' => $company1->address,
            'instansi1_hrd' => $company1->contact_person,
            'instansi1_tglmulai' => Carbon::parse($company1->internDates->first()->start_date)->translatedFormat('j F Y'),
            'instansi1_tglselesai' => Carbon::parse($company1->internDates->first()->end_date)->translatedFormat('j F Y'),
        ];
    
        foreach ($presences1 as $index => $presence) {
            $i = $index + 1;
            $formFields["1_tanggal$i"] = Carbon::parse($presence->date)->translatedFormat('l, j F Y');
            $formFields["1_checkin$i"] = $presence->check_in;
            $formFields["1_checkout$i"] = $presence->check_out ?? 'N/A';
        }
    
        foreach ($journals1 as $index => $journal) {
            $i = $index + 1;
            $formFields["1_no$i"] = $i;
            $formFields["1_bidang$i"] = $journal->work_type;
            $formFields["1_uraian$i"] = $journal->description;
            $formFields["1_tgl_jurnal$i"] = Carbon::parse($journal->date)->translatedFormat('l, j F Y');
        }
    
        $formFields['instansi2_nama'] = $company2->name;
        $formFields['instansi2_bidang'] = $company2->category;
        $formFields['instansi2_alamat'] = $company2->address;
        $formFields['instansi2_hrd'] = $company2->contact_person;
        $formFields['instansi2_tglmulai'] = Carbon::parse($company2->internDates->first()->start_date)->translatedFormat('j F Y');
        $formFields['instansi2_tglselesai'] = Carbon::parse($company2->internDates->first()->end_date)->translatedFormat('j F Y');
    
        foreach ($presences2 as $index => $presence) {
            $i = $index + 1;
            $formFields["2_tanggal$i"] = Carbon::parse($presence->date)->translatedFormat('l, j F Y');
            $formFields["2_checkin$i"] = $presence->check_in;
            $formFields["2_checkout$i"] = $presence->check_out ?? 'N/A';
        }
    
        foreach ($journals2 as $index => $journal) {
            $i = $index + 1;
            $formFields["2_no$i"] = $i;
            $formFields["2_bidang$i"] = $journal->work_type;
            $formFields["2_uraian$i"] = $journal->description;
            $formFields["2_tgl_jurnal$i"] = Carbon::parse($journal->date)->translatedFormat('l, j F Y');
        }
    
        $templatePath = public_path('template-pdf/template_2-company.pdf');
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
