<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\PresenceStatus;

class StudentController extends Controller
{
    public function getData($search=null, $status=null, $school=null, $department=null, $sort=null)
    {
        $isManager = auth()->user()->hasRole('manager');
        $isTeacher = auth()->user()->hasRole('teacher');
        $isMentor = auth()->user()->hasRole('mentor');

        $users = User::whereRelation('roles', 'name', 'student');
            if ($search) {
                $users = $users->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            }
            if ($status) {
                $users = $users->where('status', $status);
            }
            if ($school) {
                $users = $users->whereHas('schools', function($query) use ($school) {
                    $query->where('school_id', $school);
                });
            }
            if ($department) {
                $users = $users->whereHas('departments', function($query) use ($department) {
                    $query->where('department_id', $department);
                });
            }
            if ($isManager) {
                $users = $users->whereHas('schools', function($query) {
                    $query->where('school_id', auth()->user()->schools()->first()->id);
                });
            }
            if ($isTeacher) {
                $teacherDepartments = auth()->user()->departments()->pluck('id')->toArray();
                $users = $users->whereHas('departments', function ($query) use ($teacherDepartments) {
                    $query->whereIn('department_id', $teacherDepartments);
                });
            }
            if ($isMentor) {
                $users = $users->mentor(auth()->user()->companies()->first()->id);
            }
            if ($sort) {
                if ($sort[0] == '-') {
                    $sort = substr($sort, 1);
                    $sortType = 'desc';
                } else {
                    $sortType = 'asc';
                }

                $users = $users->orderBy($sort, $sortType);
            }
            $users = $users->paginate(10);

        if ($users->count() > 0) {
            $context = [
                'status' => true,
                'message' => 'Data siswa ditemukan',
                'students' => $users,
                'pagination' => $users->links()->render(),
                'search' => $search,
                'statusData' => $status,
                'school' => $school,
                'department' => $department,
                'sort' => $sort,
            ];
        } else {
            $context = [
                'status' => false,
                'message' => 'Data siswa tidak ditemukan',
                'students' => [],
                'pagination' => $users->links()->render(),
                'search' => $search,
                'statusData' => $status,
                'school' => $school,
                'department' => $department,
                'sort' => $sort,
            ];
        }

        return $context;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $school = $request->query('school');
        $department = $request->query('department');
        $sort = $request->query('sort');
        $sort = $sort ? $sort : 'name';

        $context = $this->getData($search, $status, $school, $department, $sort);

        return $context['status']
        ? view('students.index', $context)
        : view('students.index', $context)->with('error', $context['message']);
    }

    public function edit(Request $request, $id)
    {
        $company = $request->query('company');
        $company = $company ? decrypt($company) : null;
        $id = decrypt($id);

        if ($company) {
            $user = User::whereRelation('roles', 'name', 'student')
                ->where('id', $id)
                ->whereHas('companies', function($query) use ($company) {
                    $query->where('company_id', $company);
                })
                ->with(['companies' => function($query) use ($company) {
                    $query->where('company_id', $company);
                }, 'courses' => function($query) use ($company) {
                    $query->first();
                },'internDates' => function($query) use ($company) {
                    $query->where('company_id', $company);
                }])
                ->first();
        } else {
            $user = User::whereRelation('roles', 'name', 'student')
                        ->where('id', $id)
                ->first();
        }

        $company = $company ? $user->companies()->first() : null;

        if ($user) {
            $courses = Course::where('department_id', $user->departments()->first()->id)->pluck('name', 'id');

            $context = [
                'status' => true,
                'message' => 'Data siswa ditemukan',
                'student' => $user,
                'company' => $company,
                'courses' => $courses,
            ];
        } else {
            $context = [
                'status' => false,
                'message' => 'Data siswa tidak ditemukan',
                'student' => null,
                'company' => $company,
                'courses' => null
            ];
        }

        return view('students.edit', $context);
    }

    public function update(Request $request, $id)
    {
        $company = $request->query('company');
        $company = $company ? decrypt($company) : null;
        $id = decrypt($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'skills' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = User::whereRelation('roles', 'name', 'student')
            ->where('id', $id)
            ->first();

        if ($user) {
            $user->update([
                'name' => $request->name,
                'skills' => $request->skills,
            ]);
            $user->courses()->sync($request->course_id);

            if ($company) {

                $request->validate([
                    'start_date' => 'required|date',
                    'end_date' => 'required|date',
                    'extend' => 'nullable|integer',
                    'finished' => 'required|boolean',
                ]);

                $user->internDates()->where('company_id', $company)->update([
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'extend' => $request->extend,
                    'finished' => $request->finished,
                ]);

                $presencePending = PresenceStatus::where('name', 'Pending')->first('id')->id;
                $startDate = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);

                for ($i = $startDate; $i <= $endDate; $i->addDay()) {
                    $presence = $user->presences()->where('company_id', $company)->where('date', $i)->first();
                    $journal = $user->journals()->where('company_id', $company)->where('date', $i)->first();

                    if (! $presence) {
                        $user->presences()->create([
                            'company_id' => $company,
                            'date' => $i,
                            'presence_status_id' => $presencePending,
                        ]);
                    }
                    if (! $journal) {
                        $user->journals()->create([
                            'company_id' => $company,
                            'date' => $i,
                        ]);
                    }
                }
            }

            return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui');
        } else {
            return redirect()->route('students.index')->with('error', 'Data siswa tidak ditemukan');
        }
    }
}
