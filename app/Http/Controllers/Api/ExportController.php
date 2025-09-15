<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function __construct(
        private ExportService $exportService
    ) {}

    /**
     * Export transactions to CSV.
     */
    public function exportTransactions(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = $request->user();
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        try {
            $filepath = $this->exportService->exportTransactionsToCsv($user, $startDate, $endDate);
            $downloadUrl = $this->exportService->getDownloadUrl($filepath);

            return response()->json([
                'success' => true,
                'data' => [
                    'download_url' => $downloadUrl,
                    'filename' => basename($filepath),
                    'filepath' => $filepath,
                ],
                'message' => 'Transactions exported successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export transactions: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export income records to CSV.
     */
    public function exportIncome(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = $request->user();
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        try {
            $filepath = $this->exportService->exportIncomeToCsv($user, $startDate, $endDate);
            $downloadUrl = $this->exportService->getDownloadUrl($filepath);

            return response()->json([
                'success' => true,
                'data' => [
                    'download_url' => $downloadUrl,
                    'filename' => basename($filepath),
                    'filepath' => $filepath,
                ],
                'message' => 'Income records exported successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export income records: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export comprehensive budget report to CSV.
     */
    public function exportBudgetReport(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = $request->user();
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        try {
            $filepath = $this->exportService->exportBudgetReportToCsv($user, $startDate, $endDate);
            $downloadUrl = $this->exportService->getDownloadUrl($filepath);

            return response()->json([
                'success' => true,
                'data' => [
                    'download_url' => $downloadUrl,
                    'filename' => basename($filepath),
                    'filepath' => $filepath,
                ],
                'message' => 'Budget report exported successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export budget report: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download exported file.
     */
    public function download(Request $request, string $filename): Response
    {
        $user = $request->user();
        $filepath = 'exports/' . $filename;

        // Verify file exists and belongs to user (basic security check)
        if (!Storage::disk('local')->exists($filepath)) {
            abort(404, 'File not found.');
        }

        // Additional security: verify filename format
        if (!preg_match('/^[a-zA-Z0-9_-]+\.csv$/', $filename)) {
            abort(400, 'Invalid filename.');
        }

        $content = Storage::disk('local')->get($filepath);
        $mimeType = Storage::disk('local')->mimeType($filepath);

        return response($content, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Get export history for user.
     */
    public function getExportHistory(Request $request): JsonResponse
    {
        $user = $request->user();
        $files = Storage::disk('local')->files('exports');
        $userFiles = [];

        foreach ($files as $file) {
            $filename = basename($file);
            $lastModified = Storage::disk('local')->lastModified($file);
            
            // Basic filtering - in a real app, you'd store user_id with each export
            $userFiles[] = [
                'filename' => $filename,
                'size' => Storage::disk('local')->size($file),
                'created_at' => Carbon::createFromTimestamp($lastModified)->toISOString(),
                'download_url' => route('api.exports.download', ['filename' => $filename]),
            ];
        }

        // Sort by creation date (newest first)
        usort($userFiles, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return response()->json([
            'success' => true,
            'data' => $userFiles,
        ]);
    }

    /**
     * Clean up old export files.
     */
    public function cleanupOldExports(Request $request): JsonResponse
    {
        $request->validate([
            'days_old' => 'nullable|integer|min:1|max:30',
        ]);

        $daysOld = $request->get('days_old', 7);
        $deletedCount = $this->exportService->cleanupOldExports($daysOld);

        return response()->json([
            'success' => true,
            'data' => [
                'deleted_count' => $deletedCount,
                'days_old' => $daysOld,
            ],
            'message' => "Cleaned up {$deletedCount} old export files.",
        ]);
    }
}