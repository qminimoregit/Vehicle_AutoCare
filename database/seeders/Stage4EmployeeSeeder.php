<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\EmployeeAttendance;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class Stage4EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds for Stage 4 Employee & Salary Management.
     */
    public function run(): void
    {
        $employeesData = [
            [
                'employee_number' => 'EMP-0001',
                'name' => 'Nilantha Kumara',
                'mobile' => '0771234567',
                'address' => 'No. 45, Kandy Road, Kelaniya',
                'daily_wage' => 3200.00,
                'joining_date' => '2026-01-15',
                'position' => 'Senior Mechanic',
                'notes' => 'Head of engine servicing & overhaul division',
            ],
            [
                'employee_number' => 'EMP-0002',
                'name' => 'Iresha Perera',
                'mobile' => '0719876543',
                'address' => 'No. 12, Station Road, Ragama',
                'daily_wage' => 2200.00,
                'joining_date' => '2026-03-01',
                'position' => 'Detailing Specialist',
                'notes' => 'Vehicle body wash and interior shampooing',
            ],
            [
                'employee_number' => 'EMP-0003',
                'name' => 'Roshan Silva',
                'mobile' => '0754567890',
                'address' => 'No. 88, Galle Road, Dehiwala',
                'daily_wage' => 3000.00,
                'joining_date' => '2026-02-10',
                'position' => 'Auto Electrician',
                'notes' => 'Vehicle scanning, battery & starter repairs',
            ],
            [
                'employee_number' => 'EMP-0004',
                'name' => 'Hasini Kumari',
                'mobile' => '0761122334',
                'address' => 'No. 7, Temple Road, Maharagama',
                'daily_wage' => 2500.00,
                'joining_date' => '2026-04-01',
                'position' => 'Service Coordinator',
                'notes' => 'Front desk job card coordination',
            ],
        ];

        foreach ($employeesData as $data) {
            $emp = Employee::firstOrCreate(
                ['employee_number' => $data['employee_number']],
                $data
            );

            // Ensure initial wage rate history
            if ($emp->wageRates()->count() === 0) {
                $emp->wageRates()->create([
                    'daily_wage' => $emp->daily_wage,
                    'effective_from' => $emp->joining_date ?: '2026-01-01',
                    'effective_to' => null,
                    'reason' => 'Initial wage rate upon employment',
                ]);
            }
        }

        // Add some realistic attendance records for October 2026
        $nilantha = Employee::where('employee_number', 'EMP-0001')->first();
        $iresha = Employee::where('employee_number', 'EMP-0002')->first();
        $roshan = Employee::where('employee_number', 'EMP-0003')->first();
        $hasini = Employee::where('employee_number', 'EMP-0004')->first();

        // Let's mark attendance for working days up to October 20, 2026
        $today = Carbon::parse('2026-10-20');
        $start = Carbon::parse('2026-10-01');

        while ($start->lessThanOrEqualTo($today)) {
            if (!$start->isSunday()) {
                $dateStr = $start->toDateString();

                // Nilantha: full day
                EmployeeAttendance::firstOrCreate(
                    ['employee_id' => $nilantha->id, 'attendance_date' => $dateStr],
                    [
                        'status' => 'full_day',
                        'payable_day_fraction' => 1.00,
                        'applied_daily_wage' => $nilantha->daily_wage,
                        'daily_earnings' => $nilantha->daily_wage,
                    ]
                );

                // Iresha: mostly full day, half day on Saturdays
                $status = $start->isSaturday() ? 'half_day' : 'full_day';
                $fraction = $start->isSaturday() ? 0.50 : 1.00;
                EmployeeAttendance::firstOrCreate(
                    ['employee_id' => $iresha->id, 'attendance_date' => $dateStr],
                    [
                        'status' => $status,
                        'payable_day_fraction' => $fraction,
                        'applied_daily_wage' => $iresha->daily_wage,
                        'daily_earnings' => round($iresha->daily_wage * $fraction, 2),
                    ]
                );

                // Roshan: absent on Oct 10, otherwise full day
                $roshanStatus = ($start->day === 10) ? 'absent' : 'full_day';
                $roshanFraction = ($start->day === 10) ? 0.00 : 1.00;
                EmployeeAttendance::firstOrCreate(
                    ['employee_id' => $roshan->id, 'attendance_date' => $dateStr],
                    [
                        'status' => $roshanStatus,
                        'payable_day_fraction' => $roshanFraction,
                        'applied_daily_wage' => $roshan->daily_wage,
                        'daily_earnings' => round($roshan->daily_wage * $roshanFraction, 2),
                    ]
                );

                // Hasini: full day
                EmployeeAttendance::firstOrCreate(
                    ['employee_id' => $hasini->id, 'attendance_date' => $dateStr],
                    [
                        'status' => 'full_day',
                        'payable_day_fraction' => 1.00,
                        'applied_daily_wage' => $hasini->daily_wage,
                        'daily_earnings' => $hasini->daily_wage,
                    ]
                );
            }
            $start->addDay();
        }

        // Add a sample advance for Roshan: LKR 5,000 on Oct 10, 2026
        EmployeeAdvance::firstOrCreate(
            ['advance_number' => 'ADV-0001'],
            [
                'employee_id' => $roshan->id,
                'advance_date' => '2026-10-10',
                'amount' => 5000.00,
                'payment_method' => 'cash',
                'status' => 'pending',
                'notes' => 'Festival advance',
            ]
        );
    }
}
