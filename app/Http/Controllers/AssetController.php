<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\AssetResource;
use App\Exports\AssetExport;
use App\Exports\AssetMigrationTemplateExport;
use App\Imports\AssetImport;
use App\Imports\AssetMigrationImport;
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
            'type'    => 'success',
            'header'  => 'System Import Complete',
            'message' => "{$import->createdCount} created, {$import->updatedCount} updated, {$import->skippedCount} skipped.",
        ]);

        return back();
    }

    public function downloadMigrationTemplate()
    {
        return Excel::download(
            new AssetMigrationTemplateExport(),
            'asset-migration-template.xlsx'
        );
    }

    public function migrationImport(Request $request)
    {
        ini_set('max_execution_time', 300);

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $import = new AssetMigrationImport();
        Excel::import($import, $request->file('file'));

        session()->flash('notif', [
            'type'    => 'success',
            'header'  => 'Migration Import Complete',
            'message' => "{$import->createdCount} assets added, {$import->skippedCount} skipped (duplicates or missing required fields).",
        ]);

        return back();
    }

    public function exportAuditLog(Request $request)
    {
        return Excel::download(
            new AuditLogExport(
                $this->audit_export_date_from ?: null,
                $this->audit_export_date_to ?: null,
                $this->audit_export_farm ?: null,
            ),
            'audit-log-' . now()->format('Y-m-d') . '.xlsx'
        );
    }


    /**
     * Get all assets with optional pagination
     * ?per_page=20&page=1 (default 20, max 100)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min((int) $request->query('per_page', 20), 100);
            $page    = max((int) $request->query('page', 1), 1);

            $cacheKey = "api.assets.index.{$perPage}.{$page}";

            $paginated = Cache::remember($cacheKey, 3600, function () use ($perPage, $page) {
                return Asset::where('is_deleted', false)
                    ->where('is_archived', false)
                    ->paginate($perPage, ['*'], 'page', $page);
            });

            return response()->json([
                'success' => true,
                'message' => 'Assets retrieved successfully',
                'data'    => AssetResource::collection($paginated->items()),
                'meta'    => [
                    'current_page' => $paginated->currentPage(),
                    'per_page'     => $paginated->perPage(),
                    'total'        => $paginated->total(),
                    'last_page'    => $paginated->lastPage(),
                    'has_more'     => $paginated->hasMorePages(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving assets',
                'error'   => $e->getMessage()
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

    /**
     * Get all categories with their subcategories
     * Cached for 1 hour
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = Cache::remember('api.categories', 3600, function () {
                return Category::with('subcategories')
                    ->orderBy('name')
                    ->get()
                    ->map(fn ($cat) => [
                        'name'          => $cat->name,
                        'code'          => $cat->code,
                        'subcategories' => $cat->subcategories->map(fn ($sub) => [
                            'id'            => $sub->id,
                            'name'          => $sub->name,
                            'category_type' => $sub->category_type,
                        ])->values(),
                    ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully',
                'data'    => $categories,
                'count'   => $categories->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving categories',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a single category by code with its subcategories
     * Example: /api/v1/categories/itequipment
     */
    public function categoryByCode(string $code): JsonResponse
    {
        try {
            $category = Cache::remember("api.categories.{$code}", 3600, function () use ($code) {
                return Category::with('subcategories')
                    ->where('code', $code)
                    ->first();
            });

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Category retrieved successfully',
                'data'    => [
                    'name'          => $category->name,
                    'code'          => $category->code,
                    'subcategories' => $category->subcategories->map(fn ($sub) => [
                        'id'            => $sub->id,
                        'name'          => $sub->name,
                        'category_type' => $sub->category_type,
                    ])->values(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving category',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function printQr(Request $request)
    {
        $encryptedIds = explode(',', $request->query('ids', ''));

        $ids = array_map(fn($id) => decrypt($id), $encryptedIds);

        $assets = Asset::whereIn('id', $ids)
            ->whereNotNull('qr_code')
            ->get();

        return view('qr-print', compact('assets'));
    }
}