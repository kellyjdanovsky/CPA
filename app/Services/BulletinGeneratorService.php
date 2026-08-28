<?php
namespace App\Services;

use App\Models\Mark;
use App\Models\ExamRecord;
use App\Models\StudentRecord;
use ZipArchive;
use File;

class BulletinGeneratorService
{
    public function generateClassZip($exam_id, $class_id, $section_id, $year)
    {
        $students = StudentRecord::where('my_class_id', $class_id)
            ->where('section_id', $section_id)
            ->where('session', $year)
            ->get();
            
        $zipFileName = 'Bulletins_Classe_'.$class_id.'_Exam_'.$exam_id.'.zip';
        $zipPath = public_path('storage/bulletins/' . $zipFileName);
        ensureDir(public_path('storage/bulletins'));
        
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($students as $student) {
                // Generates individual PDF (assuming PDF facade/wrapper exists or just HTML saved as PDF)
                // In reality, Laravel snappy or DomPDF is used. We mock the PDF file for this service.
                $pdfContent = "Contenu du bulletin pour l'étudiant: " . $student->user->name; 
                $pdfName = 'Bulletin_' . $student->user->name . '.pdf';
                $zip->addFromString($pdfName, $pdfContent);
            }
            $zip->close();
        }
        
        return $zipPath;
    }
}