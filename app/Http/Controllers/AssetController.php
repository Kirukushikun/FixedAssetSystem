<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
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
     */
    public function index(): JsonResponse
    {
        try {
            $assets = Asset::where('is_deleted', false)
                ->where('is_archived', false)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Assets retrieved successfully',
                'data' => $assets,
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
     * Get a single asset by ID
     */
    public function show($id): JsonResponse
    {
        try {
            $asset = Asset::find($id);

            if (!$asset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asset not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Asset retrieved successfully',
                'data' => $asset
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
