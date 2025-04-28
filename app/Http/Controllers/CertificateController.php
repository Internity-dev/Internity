<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as DomPdfFacade;
use mikehaertl\pdftk\Pdf;
use Carbon\Carbon;

class CertificateController extends Controller
{
    public function exportCertificate(Request $request)
    {
        $userId = decrypt($request->input('user'));
        $companyId = decrypt($request->input('company'));

        $user = User::findOrFail($userId);
        $company = $user->companies()->findOrFail($companyId);

        return $this->generateAndDownloadNewCertificate($request, $user, $company);
    }

    public function downloadCertificate(Request $request, $id)
    {
        $certificate = Certificate::findOrFail($id);
        $user = User::findOrFail($certificate->user_id);
        $company = $user->companies()->findOrFail($certificate->company_id);

        return $this->processDownloadExistingCertificate($user, $company, $certificate->certificate_number);
    }

    private function getExistingCertificate($userId, $companyId)
    {
        return Certificate::where('user_id', $userId)
            ->where('company_id', $companyId)
            ->first();
    }

    private function processDownloadExistingCertificate($user, $company, $certificateNumber)
    {
        $formFields = $this->getCertificateFormFields($user, $company, $certificateNumber);
        $scores = $user->scores()->where('company_id', $company->id)->get();

        $fileName = now()->format('Y') . '_' . $user->departments->first()?->name. '_' . $user->name . '_certificate_' . '.pdf';
        $mergedPdfPath = $this->generateMergedPdf($formFields, $scores, $fileName);

        if ($mergedPdfPath) {
            return response()->download($mergedPdfPath, $fileName)->deleteFileAfterSend(true);
        }

        return redirect()->back()->with('error', 'Gagal membuat ulang sertifikat.');
    }

    private function generateAndDownloadNewCertificate(Request $request, $user, $company)
    {
        $noSertif = $request->input('certificate_number');
        $scores = $user->scores()->where('company_id', $company->id)->get();

        if (empty($user->nis)) {
            return redirect()->back()->with('error', 'NIS siswa belum terisi. Harap lengkapi data siswa terlebih dahulu.');
        }

        if ($scores->isEmpty()) {
            return redirect()->back()->with('error', 'Nilai tidak ditemukan! Harap tambahkan nilai terlebih dahulu.');
        }

        $this->checkCertificate($user, $company->id, $noSertif);

        $formFields = $this->getCertificateFormFields($user, $company, $noSertif);
        $fileName = now()->format('Y') . '_' . $user->departments->first()?->name . '_' . $user->name . '_certificate_' . '.pdf';
        $mergedPdfPath = $this->generateMergedPdf($formFields, $scores, $fileName);

        if ($mergedPdfPath) {
            return response()->download($mergedPdfPath, $fileName)->deleteFileAfterSend(true);
        }

        return redirect()->back()->with('error', 'Gagal membuat sertifikat.');
    }

    private function getCertificateFormFields($user, $company, $certificateNumber)
    {
        $internDate = $user->internDates()->where('company_id', $company->id)->first();
        $formattedDateOfBirth = $user->date_of_birth
            ? Carbon::parse($user->date_of_birth)->translatedFormat('j F Y')
            : 'N/A';

        return [
            'no_sertif' => 'No. ' . $certificateNumber,
            'nama_siswa' => $user->name,
            'ttl' => $formattedDateOfBirth,
            'nis' => $user->nis,
            'program_studi' => $user->departments->first()?->study_program,
            'jurusan' => $user->departments->first()?->description,
            'instansi_nama' => $company->name,
            'tgl_mulai' => $internDate ? Carbon::parse($internDate->start_date)->translatedFormat('j F Y') : 'N/A',
            'tgl_selesai' => $internDate ? Carbon::parse($internDate->end_date)->translatedFormat('j F Y') : 'N/A',
            'tgl_export' => Carbon::now()->translatedFormat('j F Y'),
            'instansi_direktur' => $company->contact_person,
            'nilai_all' => number_format($user->scores()->where('company_id', $company->id)->avg('score') ?? 0, 2),
        ];
    }

    private function generateMergedPdf($formFields, $scores, $fileName)
    {
        $templatePath = public_path('template-pdf/template_certificate_infront.pdf');

        if (!file_exists($templatePath)) {
            \Log::error('Template PDF tidak ditemukan.');
            return null;
        }

        $outputPathFront = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'front_' . $fileName;
        $pdfFront = new Pdf($templatePath);
        $resultFront = $pdfFront->fillForm($formFields)->flatten()->saveAs($outputPathFront);

        if (!$resultFront) {
            \Log::error('Gagal membuat halaman depan PDF: ' . $pdfFront->getError());
            return null;
        }

        $technicalScores = $scores->where('type', 'teknis');
        $nonTechnicalScores = $scores->where('type', 'non-teknis');

        foreach ($technicalScores as $score) {
            $score->letter = $this->convertScoreToLetter($score->score);
        }
        foreach ($nonTechnicalScores as $score) {
            $score->letter = $this->convertScoreToLetter($score->score);
        }

        $pdfBack = DomPdfFacade::loadView('pdf.certificate_back', [
            'technicalScores' => $technicalScores,
            'nonTechnicalScores' => $nonTechnicalScores,
            'avgScore' => number_format($scores->avg('score') ?? 0, 2),
        ])->setPaper('a4', 'landscape');

        $outputPathBack = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'back_' . $fileName;
        $pdfBack->save($outputPathBack);

        $mergedPdfPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName;
        $pdf = new Pdf();
        $mergeResult = $pdf->addFile($outputPathFront)
            ->addFile($outputPathBack)
            ->saveAs($mergedPdfPath);

        if (!$mergeResult) {
            \Log::error('Gagal menggabungkan PDF: ' . $pdf->getError());
            @unlink($outputPathFront);
            @unlink($outputPathBack);
            return null;
        }

        return $mergedPdfPath;
    }

    private function convertScoreToLetter($score)
    {
        if ($score >= 90) {
            return 'Sangat Baik';
        } elseif ($score >= 75) {
            return 'Baik';
        } elseif ($score >= 60) {
            return 'Cukup';
        } else {
            return 'Kurang';
        }
    }

    private function checkCertificate($user, $companyId, $noSertif)
    {
        $existingCert = Certificate::where('user_id', $user->id)
            ->where('company_id', $companyId)
            ->first();

        if (!$existingCert) {
            Certificate::create([
                'user_id' => $user->id,
                'company_id' => $companyId,
                'department_id' => $user->departments->first()?->id,
                'certificate_number' => $noSertif
            ]);
        }
    }
}
