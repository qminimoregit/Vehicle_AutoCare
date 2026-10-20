<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\EmployeeAttendance;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeAndPayrollStage4Test extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@autocare.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->staff = User::create([
            'name' => 'Staff User',
            'email' => 'staff@autocare.test',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'status' => 'active',
        ]);
    }

    // ==========================================
    // 1. EMPLOYEES TESTS
    // ==========================================

    public function test_admin_can_register_new_employee_and_initial_wage_is_recorded(): void
    {
        $response = $this->actingAs($this->admin)->post(route('payroll.employees.store'), [
            'name' => 'Nilantha Kumara',
            'mobile' => '0771234567',
            'address' => 'Colombo 05',
            'daily_wage' => 3200.00,
            'joining_date' => '2026-01-01',
            'position' => 'Senior Mechanic',
            'notes' => 'Good worker',
        ]);

        $response->assertRedirect(route('payroll.employees.index'));
        $this->assertDatabaseHas('employees', [
            'name' => 'Nilantha Kumara',
            'employee_number' => 'EMP-0001',
            'daily_wage' => 3200.00,
            'position' => 'Senior Mechanic',
            'is_active' => true,
        ]);

        $employee = Employee::first();
        $this->assertCount(1, $employee->wageRates);
        $this->assertEquals(3200.00, (float) $employee->wageRates->first()->daily_wage);
        $this->assertEquals('2026-01-01', $employee->wageRates->first()->effective_from->toDateString());
    }

    public function test_admin_can_edit_employee_details(): void
    {
        $employee = Employee::create([
            'employee_number' => 'EMP-0001',
            'name' => 'Iresha Perera',
            'mobile' => '0719876543',
            'daily_wage' => 2200.00,
            'joining_date' => '2026-02-01',
            'position' => 'Washer',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('payroll.employees.update', $employee), [
            'name' => 'Iresha Perera Updated',
            'mobile' => '0711112233',
            'address' => 'Kandy Road, Kelaniya',
            'daily_wage' => 2200.00, // unchanged wage
            'position' => 'Senior Washer',
            'is_active' => 1,
            'joining_date' => '2026-02-01',
        ]);

        $response->assertRedirect(route('payroll.employees.show', $employee));
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'name' => 'Iresha Perera Updated',
            'mobile' => '0711112233',
            'position' => 'Senior Washer',
        ]);
    }

    public function test_changing_daily_wage_preserves_wage_history_with_effective_date(): void
    {
        $employee = Employee::create([
            'employee_number' => 'EMP-0001',
            'name' => 'Roshan Silva',
            'daily_wage' => 3000.00,
            'joining_date' => '2026-01-01',
            'is_active' => true,
        ]);

        $employee->wageRates()->create([
            'daily_wage' => 3000.00,
            'effective_from' => '2026-01-01',
            'effective_to' => null,
            'reason' => 'Initial rate',
        ]);

        // Now Admin updates wage to 3500.00 effective 2026-06-01
        $response = $this->actingAs($this->admin)->put(route('payroll.employees.update', $employee), [
            'name' => 'Roshan Silva',
            'daily_wage' => 3500.00,
            'wage_effective_from' => '2026-06-01',
            'wage_reason' => 'Annual performance increment',
            'is_active' => 1,
        ]);

        $response->assertRedirect();
        $employee->refresh();
        $this->assertEquals(3500.00, (float) $employee->daily_wage);

        // Verify two wage rate records exist
        $this->assertCount(2, $employee->wageRates);

        // Old rate should be capped at 2026-05-31
        $oldRate = $employee->wageRates()->where('daily_wage', 3000.00)->first();
        $this->assertNotNull($oldRate);
        $this->assertEquals('2026-05-31', $oldRate->effective_to->toDateString());

        // New rate should be effective from 2026-06-01
        $newRate = $employee->wageRates()->where('daily_wage', 3500.00)->first();
        $this->assertNotNull($newRate);
        $this->assertEquals('2026-06-01', $newRate->effective_from->toDateString());
        $this->assertNull($newRate->effective_to);

        // Query wage applicable for earlier date (e.g. 2026-04-10) -> should be 3000.00
        $this->assertEquals(3000.00, $employee->getWageForDate('2026-04-10'));
        // Query wage applicable for future date (e.g. 2026-07-15) -> should be 3500.00
        $this->assertEquals(3500.00, $employee->getWageForDate('2026-07-15'));
    }

    public function test_admin_can_toggle_employee_active_status(): void
    {
        $employee = Employee::create([
            'employee_number' => 'EMP-0001',
            'name' => 'Hasini Kumari',
            'daily_wage' => 2500.00,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('payroll.employees.toggle-status', $employee));
        $response->assertRedirect();
        $this->assertFalse((bool) $employee->fresh()->is_active);

        // Toggle back to active
        $this->actingAs($this->admin)->patch(route('payroll.employees.toggle-status', $employee));
        $this->assertTrue((bool) $employee->fresh()->is_active);
    }

    // ==========================================
    // 2. ATTENDANCE TESTS
    // ==========================================

    public function test_admin_and_staff_can_view_attendance_and_mark_full_day(): void
    {
        $employee = Employee::create([
            'employee_number' => 'EMP-0001',
            'name' => 'Nilantha Kumara',
            'daily_wage' => 3200.00,
            'is_active' => true,
        ]);

        $today = today()->toDateString();

        $response = $this->actingAs($this->staff)->post(route('payroll.attendance.store'), [
            'attendance_date' => $today,
            'attendances' => [
                $employee->id => [
                    'status' => 'full_day',
                    'notes' => 'On time',
                ],
            ],
        ]);

        $response->assertRedirect(route('payroll.attendance.index', ['date' => $today]));
        $att = EmployeeAttendance::first();
        $this->assertNotNull($att);
        $this->assertEquals($employee->id, $att->employee_id);
        $this->assertEquals($today, $att->attendance_date->toDateString());
        $this->assertEquals('full_day', $att->status);
        $this->assertEquals(1.00, (float) $att->payable_day_fraction);
        $this->assertEquals(3200.00, (float) $att->applied_daily_wage);
        $this->assertEquals(3200.00, (float) $att->daily_earnings);
    }

    public function test_mark_half_day_calculates_half_earnings(): void
    {
        $employee = Employee::create([
            'employee_number' => 'EMP-0001',
            'name' => 'Iresha Perera',
            'daily_wage' => 2200.00,
            'is_active' => true,
        ]);

        $today = today()->toDateString();

        $this->actingAs($this->admin)->post(route('payroll.attendance.store'), [
            'attendance_date' => $today,
            'attendances' => [
                $employee->id => [
                    'status' => 'half_day',
                    'notes' => 'Left at 1 PM',
                ],
            ],
        ]);

        $att = EmployeeAttendance::first();
        $this->assertNotNull($att);
        $this->assertEquals($employee->id, $att->employee_id);
        $this->assertEquals($today, $att->attendance_date->toDateString());
        $this->assertEquals('half_day', $att->status);
        $this->assertEquals(0.50, (float) $att->payable_day_fraction);
        $this->assertEquals(2200.00, (float) $att->applied_daily_wage);
        $this->assertEquals(1100.00, (float) $att->daily_earnings);
    }

    public function test_mark_absent_and_leave_calculates_zero_or_leave_earnings(): void
    {
        $emp1 = Employee::create([
            'employee_number' => 'EMP-0001',
            'name' => 'Roshan Silva',
            'daily_wage' => 3000.00,
            'is_active' => true,
        ]);

        $emp2 = Employee::create([
            'employee_number' => 'EMP-0002',
            'name' => 'Hasini Kumari',
            'daily_wage' => 2000.00,
            'is_active' => true,
        ]);

        $today = today()->toDateString();

        $this->actingAs($this->admin)->post(route('payroll.attendance.store'), [
            'attendance_date' => $today,
            'attendances' => [
                $emp1->id => [
                    'status' => 'absent',
                    'notes' => 'Unexcused',
                ],
                $emp2->id => [
                    'status' => 'leave',
                    'notes' => 'Unpaid sick leave',
                    'is_paid_leave' => false,
                ],
            ],
        ]);

        $this->assertDatabaseHas('employee_attendance', [
            'employee_id' => $emp1->id,
            'status' => 'absent',
            'payable_day_fraction' => 0.00,
            'daily_earnings' => 0.00,
        ]);

        $this->assertDatabaseHas('employee_attendance', [
            'employee_id' => $emp2->id,
            'status' => 'leave',
            'payable_day_fraction' => 0.00,
            'daily_earnings' => 0.00,
        ]);
    }

    public function test_correct_attendance_and_prevent_duplicate_records(): void
    {
        $employee = Employee::create([
            'employee_number' => 'EMP-0001',
            'name' => 'Nilantha Kumara',
            'daily_wage' => 3200.00,
            'is_active' => true,
        ]);

        $today = today()->toDateString();

        // Step 1: Mark half day
        $this->actingAs($this->admin)->post(route('payroll.attendance.store'), [
            'attendance_date' => $today,
            'attendances' => [
                $employee->id => [
                    'status' => 'half_day',
                ],
            ],
        ]);

        $this->assertEquals(1, EmployeeAttendance::where('employee_id', $employee->id)->whereDate('attendance_date', $today)->count());
        $this->assertEquals(1600.00, (float) EmployeeAttendance::first()->daily_earnings);

        // Step 2: Correct to full day on same date
        $this->actingAs($this->admin)->post(route('payroll.attendance.store'), [
            'attendance_date' => $today,
            'attendances' => [
                $employee->id => [
                    'status' => 'full_day',
                    'notes' => 'Corrected from half to full day',
                ],
            ],
        ]);

        // Still exactly ONE record for this employee on this date
        $this->assertEquals(1, EmployeeAttendance::where('employee_id', $employee->id)->whereDate('attendance_date', $today)->count());
        $this->assertEquals(3200.00, (float) EmployeeAttendance::first()->daily_earnings);
        $this->assertEquals('full_day', EmployeeAttendance::first()->status);
    }

    public function test_monthly_calendar_reflects_marked_status(): void
    {
        $employee = Employee::create([
            'employee_number' => 'EMP-0001',
            'name' => 'Nilantha Kumara',
            'daily_wage' => 3200.00,
            'is_active' => true,
        ]);

        $date = Carbon::parse('2026-10-15');
        EmployeeAttendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => $date->toDateString(),
            'status' => 'full_day',
            'payable_day_fraction' => 1.00,
            'applied_daily_wage' => 3200.00,
            'daily_earnings' => 3200.00,
        ]);

        $response = $this->actingAs($this->admin)->get(route('payroll.attendance.index', [
            'cal_month' => 10,
            'cal_year' => 2026,
            'date' => '2026-10-15',
        ]));

        $response->assertOk();
        $response->assertSee('Attendance Calendar');
        $response->assertSee('October 2026');
    }

    public function test_simplified_checkboxes_record_full_half_and_absent_accurately(): void
    {
        $empFull = Employee::create([
            'employee_number' => 'EMP-0001',
            'name' => 'Nilantha Kumara',
            'daily_wage' => 3200.00,
            'is_active' => true,
        ]);

        $empHalf = Employee::create([
            'employee_number' => 'EMP-0002',
            'name' => 'Hasini Kumari',
            'daily_wage' => 2500.00,
            'is_active' => true,
        ]);

        $empAbsent = Employee::create([
            'employee_number' => 'EMP-0003',
            'name' => 'Roshan Silva',
            'daily_wage' => 3000.00,
            'is_active' => true,
        ]);

        $today = today()->toDateString();

        // Submit with simplified checkboxes:
        // Nilantha: present=1, half_day=0 -> Full Day
        // Hasini: present=1, half_day=1 -> Half Day
        // Roshan: present=0, half_day=0 -> Absent
        $response = $this->actingAs($this->admin)->post(route('payroll.attendance.store'), [
            'attendance_date' => $today,
            'attendances' => [
                $empFull->id => ['present' => 1, 'half_day' => 0],
                $empHalf->id => ['present' => 1, 'half_day' => 1],
                $empAbsent->id => ['present' => 0, 'half_day' => 0],
            ],
        ]);

        $response->assertRedirect(route('payroll.attendance.index', ['date' => $today]));

        $fullAtt = EmployeeAttendance::where('employee_id', $empFull->id)->whereDate('attendance_date', $today)->first();
        $this->assertNotNull($fullAtt);
        $this->assertEquals('full_day', $fullAtt->status);
        $this->assertEquals(1.00, (float) $fullAtt->payable_day_fraction);
        $this->assertEquals(3200.00, (float) $fullAtt->daily_earnings);

        $halfAtt = EmployeeAttendance::where('employee_id', $empHalf->id)->whereDate('attendance_date', $today)->first();
        $this->assertNotNull($halfAtt);
        $this->assertEquals('half_day', $halfAtt->status);
        $this->assertEquals(0.50, (float) $halfAtt->payable_day_fraction);
        $this->assertEquals(1250.00, (float) $halfAtt->daily_earnings);

        $absentAtt = EmployeeAttendance::where('employee_id', $empAbsent->id)->whereDate('attendance_date', $today)->first();
        $this->assertNotNull($absentAtt);
        $this->assertEquals('absent', $absentAtt->status);
        $this->assertEquals(0.00, (float) $absentAtt->payable_day_fraction);
        $this->assertEquals(0.00, (float) $absentAtt->daily_earnings);
    }

    // ==========================================
    // 3. SALARY & PAYROLL TESTS
    // ==========================================

    public function test_generate_monthly_salary_draft_calculates_correct_earnings(): void
    {
        $nilantha = Employee::create([
            'employee_number' => 'EMP-0001',
            'name' => 'Nilantha',
            'daily_wage' => 3200.00,
            'is_active' => true,
        ]);

        $iresha = Employee::create([
            'employee_number' => 'EMP-0002',
            'name' => 'Iresha',
            'daily_wage' => 2200.00,
            'is_active' => true,
        ]);

        // Record attendance for Nilantha: 24 full days in Oct 2026
        // 24 * 3200 = 76,800
        for ($d = 1; $d <= 24; $d++) {
            $attDate = Carbon::createFromDate(2026, 10, $d)->toDateString();
            EmployeeAttendance::create([
                'employee_id' => $nilantha->id,
                'attendance_date' => $attDate,
                'status' => 'full_day',
                'payable_day_fraction' => 1.00,
                'applied_daily_wage' => 3200.00,
                'daily_earnings' => 3200.00,
            ]);
        }

        // Record attendance for Iresha: 22 full days + 2 half days in Oct 2026
        // 22 * 2200 = 48,400 + (2 * 1100 = 2,200) = 50,600
        for ($d = 1; $d <= 22; $d++) {
            $attDate = Carbon::createFromDate(2026, 10, $d)->toDateString();
            EmployeeAttendance::create([
                'employee_id' => $iresha->id,
                'attendance_date' => $attDate,
                'status' => 'full_day',
                'payable_day_fraction' => 1.00,
                'applied_daily_wage' => 2200.00,
                'daily_earnings' => 2200.00,
            ]);
        }
        for ($d = 23; $d <= 24; $d++) {
            $attDate = Carbon::createFromDate(2026, 10, $d)->toDateString();
            EmployeeAttendance::create([
                'employee_id' => $iresha->id,
                'attendance_date' => $attDate,
                'status' => 'half_day',
                'payable_day_fraction' => 0.50,
                'applied_daily_wage' => 2200.00,
                'daily_earnings' => 1100.00,
            ]);
        }

        // Generate draft salary
        $response = $this->actingAs($this->admin)->get(route('payroll.salary.index', [
            'year' => 2026,
            'month' => 10,
            'generate' => 1,
        ]));

        $response->assertRedirect(route('payroll.salary.index', ['year' => 2026, 'month' => 10]));

        $period = PayrollPeriod::where('year', 2026)->where('month', 10)->first();
        $this->assertNotNull($period);
        $this->assertEquals(PayrollPeriod::STATUS_DRAFT, $period->status);

        // Check Nilantha's calculated draft
        $nilanthaItem = PayrollItem::where('payroll_period_id', $period->id)->where('employee_id', $nilantha->id)->first();
        $this->assertEquals(24, $nilanthaItem->full_days_count);
        $this->assertEquals(0, $nilanthaItem->half_days_count);
        $this->assertEquals(24.0, (float) $nilanthaItem->payable_days);
        $this->assertEquals(76800.00, (float) $nilanthaItem->base_earnings);
        $this->assertEquals(76800.00, (float) $nilanthaItem->gross_salary);
        $this->assertEquals(76800.00, (float) $nilanthaItem->net_salary);

        // Check Iresha's calculated draft
        $ireshaItem = PayrollItem::where('payroll_period_id', $period->id)->where('employee_id', $iresha->id)->first();
        $this->assertEquals(22, $ireshaItem->full_days_count);
        $this->assertEquals(2, $ireshaItem->half_days_count);
        $this->assertEquals(23.0, (float) $ireshaItem->payable_days);
        $this->assertEquals(50600.00, (float) $ireshaItem->base_earnings);
        $this->assertEquals(50600.00, (float) $ireshaItem->gross_salary);
        $this->assertEquals(50600.00, (float) $ireshaItem->net_salary);
    }

    public function test_record_salary_advance_and_deduct_in_payroll(): void
    {
        $employee = Employee::create([
            'employee_number' => 'EMP-0001',
            'name' => 'Roshan Silva',
            'daily_wage' => 3000.00,
            'is_active' => true,
        ]);

        // Record 20 full days attendance = 60,000
        for ($d = 1; $d <= 20; $d++) {
            EmployeeAttendance::create([
                'employee_id' => $employee->id,
                'attendance_date' => Carbon::createFromDate(2026, 10, $d)->toDateString(),
                'status' => 'full_day',
                'payable_day_fraction' => 1.00,
                'applied_daily_wage' => 3000.00,
                'daily_earnings' => 3000.00,
            ]);
        }

        // Record advance of LKR 5,000
        $advResponse = $this->actingAs($this->admin)->post(route('payroll.advances.store'), [
            'employee_id' => $employee->id,
            'advance_date' => '2026-10-10',
            'amount' => 5000.00,
            'payment_method' => 'cash',
            'notes' => 'Advance for festival',
        ]);

        $advResponse->assertRedirect();
        $this->assertDatabaseHas('employee_advances', [
            'employee_id' => $employee->id,
            'advance_number' => 'ADV-0001',
            'amount' => 5000.00,
            'status' => EmployeeAdvance::STATUS_PENDING,
        ]);

        // Generate salary
        $this->actingAs($this->admin)->get(route('payroll.salary.index', [
            'year' => 2026,
            'month' => 10,
            'generate' => 1,
        ]));

        $period = PayrollPeriod::where('year', 2026)->where('month', 10)->first();
        $item = PayrollItem::where('payroll_period_id', $period->id)->where('employee_id', $employee->id)->first();

        $this->assertEquals(60000.00, (float) $item->base_earnings);
        $this->assertEquals(5000.00, (float) $item->advances_applied);
        $this->assertEquals(60000.00, (float) $item->gross_salary);
        $this->assertEquals(55000.00, (float) $item->net_salary); // 60,000 - 5,000
    }

    public function test_prevent_duplicate_advance_deduction_across_payroll_periods(): void
    {
        $employee = Employee::create([
            'employee_number' => 'EMP-0001',
            'name' => 'Roshan Silva',
            'daily_wage' => 3000.00,
            'is_active' => true,
        ]);

        // Advance in October
        $advance = EmployeeAdvance::create([
            'advance_number' => 'ADV-0001',
            'employee_id' => $employee->id,
            'advance_date' => '2026-10-10',
            'amount' => 5000.00,
            'payment_method' => 'cash',
            'status' => EmployeeAdvance::STATUS_PENDING,
        ]);

        // October attendance
        EmployeeAttendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => '2026-10-01',
            'status' => 'full_day',
            'payable_day_fraction' => 1.00,
            'applied_daily_wage' => 3000.00,
            'daily_earnings' => 3000.00,
        ]);

        // Generate and approve October payroll
        $this->actingAs($this->admin)->get(route('payroll.salary.index', ['year' => 2026, 'month' => 10, 'generate' => 1]));
        $octPeriod = PayrollPeriod::where('year', 2026)->where('month', 10)->first();
        $this->actingAs($this->admin)->post(route('payroll.salary.approve', $octPeriod));

        // Advance must now be marked as DEDUCTED
        $this->assertEquals(EmployeeAdvance::STATUS_DEDUCTED, $advance->fresh()->status);
        $this->assertNotNull($advance->fresh()->payroll_item_id);

        // November attendance
        EmployeeAttendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => '2026-11-01',
            'status' => 'full_day',
            'payable_day_fraction' => 1.00,
            'applied_daily_wage' => 3000.00,
            'daily_earnings' => 3000.00,
        ]);

        // Generate November payroll
        $this->actingAs($this->admin)->get(route('payroll.salary.index', ['year' => 2026, 'month' => 11, 'generate' => 1]));
        $novPeriod = PayrollPeriod::where('year', 2026)->where('month', 11)->first();
        $novItem = PayrollItem::where('payroll_period_id', $novPeriod->id)->where('employee_id', $employee->id)->first();

        // November advances_applied must be ZERO! The advance was already deducted in October.
        $this->assertEquals(0.00, (float) $novItem->advances_applied);
        $this->assertEquals(3000.00, (float) $novItem->net_salary);
    }

    public function test_update_authorized_payroll_adjustments(): void
    {
        $employee = Employee::create([
            'employee_number' => 'EMP-0001',
            'name' => 'Nilantha Kumara',
            'daily_wage' => 3000.00,
            'is_active' => true,
        ]);

        EmployeeAttendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => '2026-10-01',
            'status' => 'full_day',
            'payable_day_fraction' => 1.00,
            'applied_daily_wage' => 3000.00,
            'daily_earnings' => 3000.00,
        ]);

        $this->actingAs($this->admin)->get(route('payroll.salary.index', ['year' => 2026, 'month' => 10, 'generate' => 1]));
        $item = PayrollItem::first();

        // Update adjustments: Overtime = 2,500, Bonus = 1,000, Other Deductions = 500
        $response = $this->actingAs($this->admin)->put(route('payroll.salary.update-item', $item), [
            'overtime_amount' => 2500.00,
            'bonus_amount' => 1000.00,
            'deductions' => 500.00,
            'notes' => 'Special holiday duty allowance',
        ]);

        $response->assertRedirect();
        $item->refresh();

        // Gross = 3,000 + 2,500 + 1,000 = 6,500
        $this->assertEquals(6500.00, (float) $item->gross_salary);
        // Net = 6,500 - 500 = 6,000
        $this->assertEquals(6000.00, (float) $item->net_salary);
        $this->assertEquals(6000.00, (float) $item->balance_due);
    }

    public function test_record_partial_and_final_salary_payments(): void
    {
        $employee = Employee::create([
            'employee_number' => 'EMP-0001',
            'name' => 'Nilantha Kumara',
            'daily_wage' => 3200.00,
            'is_active' => true,
        ]);

        EmployeeAttendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => '2026-10-01',
            'status' => 'full_day',
            'payable_day_fraction' => 1.00,
            'applied_daily_wage' => 3200.00,
            'daily_earnings' => 3200.00,
        ]);

        $this->actingAs($this->admin)->get(route('payroll.salary.index', ['year' => 2026, 'month' => 10, 'generate' => 1]));
        $period = PayrollPeriod::first();

        // Approve period first
        $this->actingAs($this->admin)->post(route('payroll.salary.approve', $period));
        $item = PayrollItem::first();
        $this->assertEquals('approved', $item->status);
        $this->assertEquals(3200.00, (float) $item->balance_due);

        // Step 1: Record partial payment of LKR 1,200
        $pay1 = $this->actingAs($this->admin)->post(route('payroll.salary.payments.store', $item), [
            'amount' => 1200.00,
            'payment_date' => '2026-10-31',
            'payment_method' => 'bank_transfer',
            'reference_number' => 'TXN-98765',
        ]);

        $pay1->assertRedirect();
        $item->refresh();
        $this->assertEquals(1200.00, (float) $item->paid_amount);
        $this->assertEquals(2000.00, (float) $item->balance_due);
        $this->assertEquals('partially_paid', $item->status);
        $this->assertEquals('partially_paid', $period->fresh()->status);

        // Step 2: Record final payment of remaining LKR 2,000
        $pay2 = $this->actingAs($this->admin)->post(route('payroll.salary.payments.store', $item), [
            'amount' => 2000.00,
            'payment_date' => '2026-11-01',
            'payment_method' => 'cash',
        ]);

        $pay2->assertRedirect();
        $item->refresh();
        $this->assertEquals(3200.00, (float) $item->paid_amount);
        $this->assertEquals(0.00, (float) $item->balance_due);
        $this->assertEquals('paid', $item->status);
        $this->assertEquals('paid', $period->fresh()->status);
    }

    public function test_view_and_print_payslip(): void
    {
        $employee = Employee::create([
            'employee_number' => 'EMP-0001',
            'name' => 'Nilantha Kumara',
            'daily_wage' => 3200.00,
            'is_active' => true,
        ]);

        EmployeeAttendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => '2026-10-01',
            'status' => 'full_day',
            'payable_day_fraction' => 1.00,
            'applied_daily_wage' => 3200.00,
            'daily_earnings' => 3200.00,
        ]);

        $this->actingAs($this->admin)->get(route('payroll.salary.index', ['year' => 2026, 'month' => 10, 'generate' => 1]));
        $item = PayrollItem::first();

        // View Salary Sheet
        $sheetResponse = $this->actingAs($this->admin)->get(route('payroll.salary.sheet', $item));
        $sheetResponse->assertOk();
        $sheetResponse->assertSee('Monthly Salary Sheet');
        $sheetResponse->assertSee('Nilantha Kumara');

        // View Printable Payslip
        $printResponse = $this->actingAs($this->admin)->get(route('payroll.salary.print', $item));
        $printResponse->assertOk();
        $printResponse->assertSee('Payslip / Salary Slip');
        $printResponse->assertSee('Net Salary Payable');
    }

    // ==========================================
    // 4. SECURITY & PERMISSIONS TESTS
    // ==========================================

    public function test_staff_restricted_from_accessing_salary_and_payroll_routes(): void
    {
        $employee = Employee::create([
            'employee_number' => 'EMP-0001',
            'name' => 'Nilantha Kumara',
            'daily_wage' => 3200.00,
            'is_active' => true,
        ]);

        $period = PayrollPeriod::create([
            'year' => 2026,
            'month' => 10,
            'status' => 'draft',
        ]);

        // Staff tries accessing salary index -> 403 Forbidden
        $response = $this->actingAs($this->staff)->get(route('payroll.salary.index'));
        $response->assertForbidden();

        // Staff tries registering employee -> 403 Forbidden
        $regResponse = $this->actingAs($this->staff)->post(route('payroll.employees.store'), [
            'name' => 'Test Employee',
            'daily_wage' => 3000,
        ]);
        $regResponse->assertForbidden();

        // Staff tries approving payroll -> 403 Forbidden
        $appResponse = $this->actingAs($this->staff)->post(route('payroll.salary.approve', $period));
        $appResponse->assertForbidden();
    }
}
