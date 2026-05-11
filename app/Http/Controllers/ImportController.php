<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MembersExport;
use App\Imports\MembersImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportController extends Controller
{
    public function importMembership(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
            'update_existing' => 'sometimes|string|in:true,false,1,0',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        try {
            // Get update_existing flag from request, default to true
            $updateExistingValue = $request->input('update_existing', 'true');
            $updateExisting = in_array($updateExistingValue, ['true', '1', true, 1]);

            // Import the file
            $import = new MembersImport($updateExisting);
            Excel::import($import, $request->file('file'));

            // Generate the report
            $reportCsv = $import->generateReportCsv();
            $successCount = $import->getSuccessCount();
            $failureCount = $import->getFailureCount();

            // Create report file
            $reportFileName = 'import_report_' . date('Y-m-d_H-i-s') . '.csv';
            $reportPath = storage_path('app/temp/' . $reportFileName);
            
            // Ensure temp directory exists
            $tempDir = storage_path('app/temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            file_put_contents($reportPath, $reportCsv);

            // Return the report file for download
            return response()->download($reportPath, $reportFileName, [
                'Content-Type' => 'text/csv',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
