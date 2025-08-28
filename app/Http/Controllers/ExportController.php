<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Citizen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MembersExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Export membership data in the specified format.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportMembership(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'format' => 'required|in:excel,csv,pdf',
            'include_headers' => 'sometimes|boolean',
            'fields' => 'required|array|min:1',
            'fields.*' => 'string',
            'filters' => 'sometimes|array',
            'filters.county_id' => 'nullable|exists:counties,id',
            'filters.constituency_id' => 'nullable|exists:constituencies,id',
            'filters.ward_id' => 'nullable|exists:wards,id',
            'filters.polling_center_id' => 'nullable|exists:polling_centers,id',
            'filters.date_from' => 'nullable|date',
            'filters.date_to' => 'nullable|date|after_or_equal:filters.date_from',
        ]);

        // Build the base query
        $query = DB::table('citizens as c')
            ->select([
                DB::raw("CONCAT(p.last_name) AS surname"),
                DB::raw("CONCAT(COALESCE(p.first_name, ''), ' ', COALESCE(p.middle_name, '')) AS other_names"),
                'c.national_identification_number AS id_number',
                'p.telephone',
                'u.email',
                'p.gender',
                'p.date_of_birth',
                'cnt.name AS county',
                'con.name AS constituency',
                'w.name AS ward',
                'pc.name AS polling_station',
                'p.income_source AS occupation',
                'p.highest_level_of_education AS education_level',
                'p.disability_status',
                'p.ncpwd_number',
                'c.created_at'
            ])
            ->leftJoin('users as u', 'c.user_id', '=', 'u.id')
            ->leftJoin('profiles as p', 'c.user_id', '=', 'p.user_id')
            ->leftJoin('counties as cnt', 'c.county_id', '=', 'cnt.id')
            ->leftJoin('constituencies as con', 'c.constituency_id', '=', 'con.id')
            ->leftJoin('wards as w', 'c.ward_id', '=', 'w.id')
            ->leftJoin('polling_centers as pc', 'c.polling_center_id', '=', 'pc.id')
            ->orderBy('c.created_at', 'DESC');

        // Apply filters
        if (!empty($validated['filters'])) {
            $filters = $validated['filters'];
            
            // Filter by location
            if (!empty($filters['county_id'])) {
                $query->where('c.county_id', $filters['county_id']);
            }
            
            if (!empty($filters['constituency_id'])) {
                $query->where('c.constituency_id', $filters['constituency_id']);
            }
            
            if (!empty($filters['ward_id'])) {
                $query->where('c.ward_id', $filters['ward_id']);
            }
            
            if (!empty($filters['polling_center_id'])) {
                $query->where('c.polling_center_id', $filters['polling_center_id']);
            }
            
            // Filter by date range
            if (!empty($filters['date_from'])) {
                $query->whereDate('c.created_at', '>=', $filters['date_from']);
            }
            
            if (!empty($filters['date_to'])) {
                $query->whereDate('c.created_at', '<=', $filters['date_to']);
            }
        }

        // Get the data
        $data = $query->get();

        // Map the selected fields from the request
        $selectedFields = $validated['fields'];
        $includeHeaders = $validated['include_headers'] ?? true;

        // Filter the data to only include selected fields
        $filteredData = $data->map(function ($item) use ($selectedFields) {
            $filtered = [];
            foreach ($selectedFields as $field) {
                // Handle field mapping for PDF/excel export
                $filtered[$field] = $item->$field ?? '';
            }
            return $filtered;
        });

        // Export based on format
        switch ($validated['format']) {
            case 'excel':
                return Excel::download(new MembersExport($filteredData, $selectedFields, $includeHeaders), 'membership.xlsx');
                
            case 'csv':
                return Excel::download(new MembersExport($filteredData, $selectedFields, $includeHeaders), 'membership.csv');
                
            case 'pdf':
                // Filter out unwanted fields
                $fieldsToExclude = ['other_names', 'id_number', 'email', 'occupation', 'education_level', 'disability_status', 'ncpwd_number'];
                $selectedFields = array_values(array_diff($selectedFields, $fieldsToExclude));
                
                // Filter the data to exclude the unwanted fields
                $filteredData = $data->map(function ($item) use ($selectedFields) {
                    $filtered = [];
                    foreach ($selectedFields as $field) {
                        $filtered[$field] = $item->$field ?? '';
                    }
                    return $filtered;
                });
                
                $pdf = Pdf::loadView('exports.members-pdf', [
                    'data' => $filteredData,
                    'headers' => $includeHeaders ? $selectedFields : [],
                    'fields' => $selectedFields
                ])->setPaper('a4', 'landscape');
                
                return $pdf->download('members.pdf');
                
            default:
                return redirect()->back()->with('error', 'Invalid export format.');
        }
    }
}