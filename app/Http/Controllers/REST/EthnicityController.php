<?php

namespace App\Http\Controllers\REST;

use App\Models\Ethnicity;
use App\Models\EthnicityType;
use App\Models\EthnicityCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EthnicityController extends Controller
{
    /**
     * Get all ethnicities
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $ethnicities = Ethnicity::select('id', 'name', 'slug', 'description', 'type_id', 'category_id')
                ->with([
                    'type:id,name', 
                    'category:id,name'
                ])
                ->orderBy('name')
                ->get()
                ->map(function($ethnicity) {
                    return [
                        'id' => $ethnicity->id,
                        'name' => $ethnicity->name,
                        'slug' => $ethnicity->slug,
                        'description' => $ethnicity->description,
                        'type' => $ethnicity->type ? [
                            'id' => $ethnicity->type->id,
                            'name' => $ethnicity->type->name
                        ] : null,
                        'category' => $ethnicity->category ? [
                            'id' => $ethnicity->category->id,
                            'name' => $ethnicity->category->name
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $ethnicities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch ethnicities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ethnicities by category
     *
     * @param EthnicityCategory $category
     * @return JsonResponse
     */
    public function getByCategory(EthnicityCategory $category): JsonResponse
    {
        try {
            $ethnicities = Ethnicity::where('category_id', $category->id)
                ->select('id', 'name', 'slug')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $ethnicities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch ethnicities by category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ethnicities by type
     *
     * @param EthnicityType $type
     * @return JsonResponse
     */
    public function getByType(EthnicityType $type): JsonResponse
    {
        try {
            $ethnicities = Ethnicity::where('type_id', $type->id)
                ->select('id', 'name', 'slug')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $ethnicities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch ethnicities by type',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a single ethnicity
     *
     * @param Ethnicity $ethnicity
     * @return JsonResponse
     */
    public function show(Ethnicity $ethnicity): JsonResponse
    {
        try {
            $ethnicity->load(['type', 'category']);
            
            return response()->json([
                'success' => true,
                'data' => $ethnicity
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch ethnicity',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
