<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\AssetResource;
use App\Exports\AssetExport;
use App\Imports\AssetImport;
use Maatwebsite\Excel\Facades\Excel;


class AssetController extends Controller
{
    /**
     * Export employees data to Excel
     */
    public function export()
    {
        return Excel::download(
            new AssetExport(), 
            'FIXED ASSET System - Assets.xlsx'
        );
    }

    public function import(Request $request){
        ini_set('max_execution_time', 300); // 5 minutes

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $import = new AssetImport;
        Excel::import($import, $request->file('file'));

        session()->flash('notif', [
            'type' => 'success',
            'header' => 'Import Successful',
            'message' => "Import finished: {$import->createdCount} new rows, {$import->updatedCount} updated."
        ]);

        return back();
    }


    /**
     * Get all assets
     * Returns a JSON array of all assets
     * Cached for 60 minutes
     */
    public function index(): JsonResponse
    {
        try {
            // Cache for 1 hour (3600 seconds)
            $assets = Cache::remember('api.assets.index', 3600, function () {
                return Asset::where('is_deleted', false)
                    ->where('is_archived', false)
                    ->get();
            });

            return response()->json([
                'success' => true,
                'message' => 'Assets retrieved successfully',
                'data' => AssetResource::collection($assets),
                'count' => $assets->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving assets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search assets by keyword
     * Example: /api/v1/assets/search?search=Asus
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $keyword = $request->query('search');

            if (!$keyword) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search keyword is required'
                ], 400);
            }

            // Create cache key based on search term
            $cacheKey = 'api.assets.search.' . md5($keyword);

            $assets = Cache::remember($cacheKey, 3600, function () use ($keyword) {
                $search = '%' . $keyword . '%';
                
                return Asset::where('is_deleted', false)
                    ->where('is_archived', false)
                    ->where(function($query) use ($search) {
                        $query->where('ref_id', 'LIKE', $search)
                            ->orWhere('category_type', 'LIKE', $search)
                            ->orWhere('category', 'LIKE', $search)
                            ->orWhere('sub_category', 'LIKE', $search)
                            ->orWhere('brand', 'LIKE', $search)
                            ->orWhere('model', 'LIKE', $search)
                            ->orWhere('status', 'LIKE', $search)
                            ->orWhere('condition', 'LIKE', $search);
                    })
                    ->get();
            });

            return response()->json([
                'success' => true,
                'message' => 'Search completed successfully',
                'data' => AssetResource::collection($assets),
                'count' => $assets->count(),
                'search_term' => $keyword
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching assets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a single asset by ID
     * Cached for 60 minutes
     */
    public function show($id): JsonResponse
    {
        try {
            // Cache individual asset for 1 hour
            $asset = Cache::remember("api.assets.show.{$id}", 3600, function () use ($id) {
                return Asset::where("ref_id", $id)->first();
            });

            if (!$asset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asset not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Asset retrieved successfully',
                'data' => new AssetResource($asset)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving asset',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}