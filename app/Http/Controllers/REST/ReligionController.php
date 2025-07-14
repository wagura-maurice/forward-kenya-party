<?php

namespace App\Http\Controllers\REST;

use App\Models\Religion;
use App\Models\ReligionType;
use App\Models\ReligionCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReligionController extends Controller
{
    /**
     * Get all religions
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $religions = Religion::select('id', 'name', 'slug', 'description', 'type_id', 'category_id')
                ->with([
                    'type:id,name', 
                    'category:id,name'
                ])
                ->orderBy('name')
                ->get()
                ->map(function($religion) {
                    return [
                        'id' => $religion->id,
                        'name' => $religion->name,
                        'slug' => $religion->slug,
                        'description' => $religion->description,
                        'type' => $religion->type ? [
                            'id' => $religion->type->id,
                            'name' => $religion->type->name
                        ] : null,
                        'category' => $religion->category ? [
                            'id' => $religion->category->id,
                            'name' => $religion->category->name
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $religions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch religions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get religions by category
     *
     * @param ReligionCategory $category
     * @return JsonResponse
     */
    public function getByCategory(ReligionCategory $category): JsonResponse
    {
        try {
            $religions = Religion::where('category_id', $category->id)
                ->select('id', 'name', 'slug')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $religions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch religions by category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get religions by type
     *
     * @param ReligionType $type
     * @return JsonResponse
     */
    public function getByType(ReligionType $type): JsonResponse
    {
        try {
            $religions = Religion::where('type_id', $type->id)
                ->select('id', 'name', 'slug')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $religions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch religions by type',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a single religion
     *
     * @param Religion $religion
     * @return JsonResponse
     */
    public function show(Religion $religion): JsonResponse
    {
        try {
            $religion->load(['type', 'category']);
            
            return response()->json([
                'success' => true,
                'data' => $religion
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch religion',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
