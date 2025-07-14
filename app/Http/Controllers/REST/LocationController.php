<?php

namespace App\Http\Controllers\REST;

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
     * Get all counties
     *
     * @return JsonResponse
     */
    public function getCounties(): JsonResponse
    {
        try {
            $counties = County::select('id', 'name', 'iso_code as code')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $counties
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch counties',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sub-counties by county
     *
     * @param County $county
     * @return JsonResponse
     */
    public function getSubCounties(County $county): JsonResponse
    {
        try {
            $subCounties = SubCounty::where('county_id', $county->id)
                ->select('id', 'name', 'county_id')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $subCounties
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sub-counties',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get constituencies by sub-county
     *
     * @param SubCounty $subCounty
     * @return JsonResponse
     */
    public function getConstituenciesBySubCounty(SubCounty $subCounty): JsonResponse
    {
        try {
            $constituencies = Constituency::where('sub_county_id', $subCounty->id)
                ->select('id', 'name', 'sub_county_id', 'county_id')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $constituencies
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch constituencies',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get constituencies by county
     *
     * @param County $county
     * @return JsonResponse
     */
    public function getConstituenciesByCounty(County $county): JsonResponse
    {
        try {
            $constituencies = Constituency::where('county_id', $county->id)
                ->select('id', 'name', 'county_id', 'sub_county_id')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $constituencies
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch constituencies',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wards by constituency
     *
     * @param Constituency $constituency
     * @return JsonResponse
     */
    public function getWards(Constituency $constituency): JsonResponse
    {
        try {
            $wards = Ward::where('constituency_id', $constituency->id)
                ->select('id', 'name', 'constituency_id')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $wards
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch wards',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
