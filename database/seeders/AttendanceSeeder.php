<?php

// database/seeders/AttendanceSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Activities;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample attendance data for the last 10 days
        $Activities = [
            'Menghadiri meeting pagi',
            'Menyelesaikan laporan bulanan',
            'Review kode program',
            'Diskusi dengan tim development',
            'Update dokumentasi project',
            'Testing aplikasi baru',
            'Koordinasi dengan client',
            'Maintenance server',
            'Training internal',
            'Evaluasi kinerja tim'
        ];

        for ($i = 9; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $isPresent = rand(0, 10) > 2; // 80% chance present

            $attendance = Attendance::create([
                'date' => $date->format('Y-m-d'),
                'present' => $isPresent,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            // Add activities only if present
            if ($isPresent) {
                $numActivities = rand(1, 4); // 1-4 activities per day
                $selectedActivities = array_rand($Activities, $numActivities);

                if (!is_array($selectedActivities)) {
                    $selectedActivities = [$selectedActivities];
                }

                foreach ($selectedActivities as $activityIndex) {
                    Activities::create([
                        'attendance_id' => $attendance->id,
                        'activity' => $Activities[$activityIndex],
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                }
            }
        }
    }
}
