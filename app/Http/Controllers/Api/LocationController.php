<?php

namespace App\Http\Controllers\Api;

use App\Models\Ward;
use App\Models\County;
use App\Models\SubCounty;
use App\Models\Constituency;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    /**
     * Get locations based on type and parent ID
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getLocations(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'nullable|string|in:county,subcounty,constituency,ward',
            'parent_id' => 'nullable|integer',
            'country_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $type = $request->input('type');
        $parentId = $request->input('parent_id');
        $countryId = $request->input('country_id', 1); // Default to Kenya (assuming 1 is Kenya's ID)

        // If no type is specified, return all locations grouped
        if (!$type) {
            $counties = County::select('id', 'country_id', 'name')
                ->when($countryId, function($query) use ($countryId) {
                    return $query->where('country_id', $countryId);
                })
                ->get();
            
            $subCounties = SubCounty::select('id', 'county_id', 'name')
                ->when($parentId, function($query) use ($parentId) {
                    return $query->where('county_id', $parentId);
                })
                ->get();
            
            $constituencies = Constituency::select('id', 'county_id', 'name')
                ->when($parentId, function($query) use ($parentId) {
                    return $query->where('county_id', $parentId);
                })
                ->get();
            
            $wards = Ward::select('id', 'constituency_id', 'name')
                ->when($parentId, function($query) use ($parentId) {
                    return $query->where('constituency_id', $parentId);
                })
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'counties' => $counties->groupBy('country_id'),
                    'subCounties' => $subCounties->groupBy('county_id'),
                    'constituencies' => $constituencies->groupBy('county_id'),
                    'wards' => $wards->groupBy('constituency_id')
                ]
            ]);
        }

        // Return specific location type
        switch (strtolower($type)) {
            case 'county':
                $query = County::select('id', 'country_id', 'name')
                    ->when($countryId, function($q) use ($countryId) {
                        return $q->where('country_id', $countryId);
                    });
                break;
                
            case 'subcounty':
                $query = SubCounty::select('id', 'county_id', 'name')
                    ->when($parentId, function($q) use ($parentId) {
                        return $q->where('county_id', $parentId);
                    });
                break;
                
            case 'constituency':
                $query = Constituency::select('id', 'county_id', 'name')
                    ->when($parentId, function($q) use ($parentId) {
                        return $q->where('county_id', $parentId);
                    });
                break;
                
            case 'ward':
                $query = Ward::select('id', 'constituency_id', 'name')
                    ->when($parentId, function($q) use ($parentId) {
                        return $q->where('constituency_id', $parentId);
                    });
                break;
                
            default:
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid location type specified'
                ], 400);
        }

        $locations = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $locations
        ]);
    }
}
