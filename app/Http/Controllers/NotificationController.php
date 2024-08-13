<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\EndDateReminder;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    public function endDateReminder($day)
    {
        // Get student that has interndates and the end_date is $day days from now
        $students = User::whereHas('internDates', function ($query) use ($day) {
            $query->where('end_date', Carbon::now()->addDays($day)->format('Y-m-d'));
        })->get();

        // Send email to each student
        foreach ($students as $student) {
            $today = Carbon::now()->format('Y-m-d');
            $end = Carbon::now()->addDays($day)->format('Y-m-d');
            $company = $student->internDates()->where('end_date', $end)->first()->company;
            $department = $company->department;
            $teachers = User::whereHas('departments', function ($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->whereHas('roles', function ($query) {
                $query->where('name', 'kepala bengkel')
                    ->orWhere('name', 'kepala program');
            })
            ->get();

            foreach ($teachers as $teacher) {
                Mail::to($teacher->email)->send(new EndDateReminder($teacher, $student, $company, $today, $end));
            }
        }

        return response()->json([
            'message' => 'Email reminder sent',
        ], 200);
    }
}
