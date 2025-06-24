<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Activities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display the attendance form and history
     */
    public function index()
    {
        return view('attendance');
    }

    /**
     * Store a new attendance record
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'present' => 'boolean',
            'activities' => 'array',
            'activities.*.name' => 'string|nullable'
        ]);

        try {
            DB::beginTransaction();

            // Create or update attendance record
            $attendance = Attendance::updateOrCreate(
                ['date' => $request->date],
                ['present' => $request->boolean('present')]
            );

            // Delete existing activities for this attendance
            $attendance->activities()->delete();

            // Add new activities if present and activities exist
            if ($request->boolean('present') && $request->has('activities')) {
                foreach ($request->activities as $activityData) {
                    if (!empty($activityData['name'])) {
                        Activities::create([
                            'attendance_id' => $attendance->id,
                            'activity' => $activityData['name']
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kehadiran berhasil disimpan',
                'data' => $attendance->load('activities')
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan kehadiran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance history for a specific date or all
     */
    public function history(Request $request)
    {
        try {
            $query = Attendance::with('activities');

            // Filter by date if provided
            if ($request->has('date') && $request->date) {
                $query->whereDate('date', $request->date);
            }

            $attendances = $query->orderBy('date', 'desc')
                                ->orderBy('created_at', 'desc')
                                ->get();

            // Transform data to match frontend expectations
            $transformedData = $attendances->map(function ($attendance) {
                return [
                    'id' => $attendance->id,
                    'date' => $attendance->date,
                    'is_present' => $attendance->present,
                    'created_at' => $attendance->created_at->toISOString(),
                    'updated_at' => $attendance->updated_at->toISOString(),
                    'activities' => $attendance->activities->map(function ($activity) {
                        return [
                            'id' => $activity->id,
                            'name' => $activity->activity
                        ];
                    })
                ];
            });

            return response()->json($transformedData);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat riwayat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an attendance record
     */
    public function destroy($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);
            $attendance->delete(); // Activities will be deleted automatically due to cascade

            return response()->json([
                'success' => true,
                'message' => 'Data kehadiran berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance statistics
     */
    public function statistics(Request $request)
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

            $stats = [
                'total_days' => Attendance::whereBetween('date', [$startDate, $endDate])->count(),
                'present_days' => Attendance::whereBetween('date', [$startDate, $endDate])
                                           ->where('present', true)->count(),
                'absent_days' => Attendance::whereBetween('date', [$startDate, $endDate])
                                          ->where('present', false)->count(),
                'total_activities' => Activities::whereHas('attendance', function($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                })->count()
            ];

            $stats['attendance_percentage'] = $stats['total_days'] > 0
                ? round(($stats['present_days'] / $stats['total_days']) * 100, 2)
                : 0;

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik: ' . $e->getMessage()
            ], 500);
        }
    }
}
