<?php

namespace App\Http\Controllers\hrm;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class EmployeeSessionController extends Controller
{
    public function attendance_by_employee(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'view', Attendance::class);

        $attendances = Attendance::where('employee_id', $id)
            ->where('deleted_at', '=', null)
            ->orderBy('date', 'desc')
            ->get([
                'id',
                'employee_id',
                'company_id',
                'date',
                'clock_in',
                'clock_out',
                'total_work',
                'status',
            ]);

        return response()->json([
            'attendances' => $attendances,
        ]);
    }
}
