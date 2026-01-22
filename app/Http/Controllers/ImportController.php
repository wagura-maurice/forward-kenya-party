<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MembersExport;
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
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // 
    }
}
