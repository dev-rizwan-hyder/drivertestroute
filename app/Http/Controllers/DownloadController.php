<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    /**
     * Download examiner sheet
     */
    public function examinerSheet($type)
    {
        $validTypes = ['g1', 'g2'];
        
        if (!in_array($type, $validTypes)) {
            abort(404, 'Sheet type not found');
        }

        $fileName = $type === 'g1' ? 'g1-examiner-sheet.pdf' : 'g2-examiner-sheet.pdf';
        $filePath = public_path('sheet/' . $fileName);

        if (!file_exists($filePath)) {
            abort(404, 'Examiner sheet not found');
        }

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }
}
