<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Course;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\PresenceStatus;

class StudentController extends Controller
{
    public function getData($search=null, $status=null, $school=null, $department=null, $sort=null)
    {
        $isManager = auth()->user()->hasRole('manager');
        $isKaprog = auth()->user()->hasRole('kepala program');
        $isKabeng = auth()->user()->hasRole('kepala bengkel');
        $isTeacher = auth()->user()->hasRole('teacher');
        $isMentor = auth()->user()->hasRole('mentor');

        $users = User::whereRelation('roles', 'name', 'student')
        ->with(['companies', 'internDates', 'schools', 'departments']); 
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
            if ($isKaprog || $isKabeng) {
                $users = $users->kaprog(auth()->user()->departments()->first()->id);
            }
            if ($isTeacher) {
                $users = $users->teacher();
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
                ->with(['companies', 'internDates'])
                ->first();
        } else {
            $user = User::whereRelation('roles', 'name', 'student')
                        ->where('id', $id)
                ->first();
        }
        if (auth()->user()->hasRole('mentor')) {
            $mentorCompanyIds = auth()->user()->companies->pluck('id')->toArray();
            $companiesData = $user->companies->filter(function ($company) use ($mentorCompanyIds) {
                return in_array($company->id, $mentorCompanyIds);
            })->map(function ($company) use ($user) {
                $internDate = $user->internDates->where('company_id', $company->id)->first();
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'start_date' => $internDate?->start_date,
                    'end_date' => $internDate?->end_date,
                    'extend' => $internDate?->extend,
                    'finished' => $internDate?->finished,
                ];
            });
        } else {
            $companiesData = $user->companies->map(function ($company) use ($user) {
                $internDate = $user->internDates->where('company_id', $company->id)->first();
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'start_date' => $internDate?->start_date,
                    'end_date' => $internDate?->end_date,
                    'extend' => $internDate?->extend,
                    'finished' => $internDate?->finished,
                ];
            });
        }

        if ($user) {
            $courses = Course::where('department_id', $user->departments()->first()->id)->pluck('name', 'id');

            $context = [
                'status' => true,
                'message' => 'Data siswa ditemukan',
                'student' => $user,
                'companiesData' => $companiesData,
                'courses' => $courses,
            ];
        } else {
            $context = [
                'status' => false,
                'message' => 'Data siswa tidak ditemukan',
                'student' => null,
                'companiesData' => $companiesData,
                'courses' => null
            ];
        }

        // dd($context);

        return view('students.edit', $context);
    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'skills' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'companies.*.start_date' => 'nullable|date',
            'companies.*.end_date' => 'nullable|date',
            'companies.*.extend' => 'nullable|integer',
            'companies.*.finished' => 'nullable|boolean',
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

            if ($request->has('companies')) {
                foreach ($request->companies as $companyId => $data) {
                    $company = Company::find($companyId);
                    if ($company) {
                        $user->internDates()->updateOrCreate(
                            ['company_id' => $companyId],
                            [
                                'start_date' => $data['start_date'] ?? null,
                                'end_date' => $data['end_date'] ?? null,
                                'extend' => $data['extend'] ?? 0,
                                'finished' => $data['finished'] ?? 0,
                            ]
                        );

                        $presencePending = PresenceStatus::where('name', 'Pending')->first('id')->id;
                        $startDate = Carbon::parse($data['start_date']);
                        $endDate = Carbon::parse($data['end_date']);

                        for ($i = $startDate; $i <= $endDate; $i->addDay()) {
                            $presence = $user->presences()->where('company_id', $companyId)->where('date', $i)->first();
                            if (!$presence) {
                                $user->presences()->create([
                                    'company_id' => $companyId,
                                    'date' => $i,
                                    'presence_status_id' => $presencePending,
                                ]);
                            }

                            $journal = $user->journals()->where('company_id', $companyId)->where('date', $i)->first();
                            if (!$journal) {
                                $user->journals()->create([
                                    'company_id' => $companyId,
                                    'date' => $i,
                                ]);
                            }
                        }
                    }
                }
            }
            return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui');
        } else {
            return redirect()->route('students.index')->with('error', 'Data siswa tidak ditemukan');
        }
    }
}
