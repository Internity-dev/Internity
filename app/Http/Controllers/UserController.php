<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Course;
use App\Models\School;
use App\Models\Company;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:user-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update', 'updateStatus']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    public function getData($search=null, $status=null, $school=null, $department=null, $sort=null)
    {
        $isSuperadmin = auth()->user()->hasRole('super-admin');
        $isManager = auth()->user()->hasRole('manager');
        $isKaprog = auth()->user()->hasRole('kepala program');
        $isKabeng = auth()->user()->hasRole('kepala bengkel');
        $isTeacher = auth()->user()->hasRole('teacher');

        $users = User::query();
        if ($search) {
            $users = $users->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        }
        if ($status) {
            $users->where('status', $status);
        }
        if ($school) {
            $users->whereHas('schools', function($query) use ($school) {
                $query->where('school_id', $school);
            });
        }
        if ($department) {
            $users->whereHas('departments', function($query) use ($department) {
                $query->where('department_id', $department);
            });
        }
        if (! $isSuperadmin) {
            $users->whereHas('roles', function($query) {
                $query->where('name', '!=', 'super-admin');
            });
        }
        if ($isManager) {
            $users->manager(auth()->user()->schools()->first()->id);
        }
        if ($isKaprog || $isKabeng) {
            $users->kaprog(auth()->user()->departments()->first()->id);
        }
        if ($isTeacher) {
            $users = $users->teacher();
        }
        if ($sort) {
            if ($sort[0] == '-') {
                $sort = substr($sort, 1);
                $sortType = 'desc';
            } else {
                $sortType = 'asc';
            }

            $users->orderBy($sort, $sortType);
        }
        $users = $users->paginate(10);

        if ($users->count() > 0) {
            $context = [
                'status' => true,
                'message' => 'Data user ditemukan',
                'users' => $users,
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
                'message' => 'Data user tidak ditemukan',
                'users' => [],
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
        $role = $request->query('role');
        $status = $request->query('status');
        $school = $request->query('school');
        $department = $request->query('department');
        $sort = $request->query('sort');

        $context = $this->getData($search, $status, $school, $department, $sort);

        return $context['status']
        ? view('users.index', $context)
        : view('users.index', $context)->with('error', $context['message']);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $isManager = auth()->user()->hasRole('manager');
        $isKaprog = auth()->user()->hasRole('kepala program');
        $isKabeng = auth()->user()->hasRole('kepala bengkel');

        if ($isManager) {
            $roles = Role::where('name', '!=', 'admin')->where('name', '!=', 'super-admin')->pluck('name', 'id');
            $schoolId = auth()->user()->schools()->first()->id;
            $schools = School::find($schoolId)->pluck('name', 'id');
            $departments = Department::where('school_id', $schoolId)->pluck('name', 'id');
            $courses = Course::join('departments', 'courses.department_id', '=', 'departments.id')
                ->join('schools', 'departments.school_id', '=', 'schools.id')
                ->where('schools.id', $schoolId)
                ->pluck('courses.name', 'courses.id');
            $companies = Company::join('departments', 'companies.department_id', '=', 'departments.id')
                ->join('schools', 'departments.school_id', '=', 'schools.id')
                ->where('schools.id', $schoolId)
                ->get();
            $modelIds = DB::table('model_has_roles')
                ->where('role_id', 6)
                ->pluck('model_id');
            $students = User::whereIn('id', $modelIds)->get();
        } elseif ($isKaprog) {
            $roles = Role::where('name', 'student')->orWhere('name', 'kepala program')->orWhere('name', 'mentor')->pluck('name', 'id');
            $schoolId = auth()->user()->schools()->first()->id;
            $schools = School::find($schoolId)->pluck('name', 'id');
            $departmentId = auth()->user()->departments()->first()->id;
            $departments = Department::where('id', $departmentId)->pluck('name', 'id');
            $courses = Course::where('department_id', $departmentId)->pluck('name', 'id');
            $companies = Company::where('department_id', $departmentId)->get();
            $modelIds = DB::table('model_has_roles')
                ->where('role_id', 6)
                ->pluck('model_id');
            $students = User::whereIn('id', $modelIds)->get();
        } elseif ($isKabeng) {
            $roles = Role::where('name', 'mentor')->pluck('name', 'id');
            $schoolId = auth()->user()->schools()->first()->id;
            $schools = School::find($schoolId)->pluck('name', 'id');
            $departmentId = auth()->user()->departments()->first()->id;
            $departments = Department::where('id', $departmentId)->pluck('name', 'id');
            $courses = Course::where('department_id', $departmentId)->pluck('name', 'id');
            $companies = Company::where('department_id', $departmentId)->get();
            $modelIds = DB::table('model_has_roles')
                ->where('role_id', 6)
                ->pluck('model_id');
            $students = User::whereIn('id', $modelIds)->get();
        } else {
            $roles = auth()->user()->hasRole('super-admin')
                ? Role::pluck('name', 'id')
                : Role::where('name', '!=', 'super-admin')->pluck('name', 'id');

            $schools = School::pluck('name', 'id');
            $departments = Department::pluck('name', 'id');
            $courses = Course::pluck('name', 'id');
            $companies = Company::all();
            $modelIds = DB::table('model_has_roles')
                ->where('role_id', 6)
                ->pluck('model_id');
            $students = User::whereIn('id', $modelIds)->get();
        }

        return view('users.create', compact('schools', 'departments', 'courses', 'roles', 'companies', 'students'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|same:confirm-password',
            'role_id' => 'required|exists:roles,id',
            'school_id' => 'nullable|exists:schools,id',
            'department_id' => 'nullable|exists:departments,id',
            'course_id' => 'nullable|exists:courses,id',
            'company_id' => 'nullable|exists:companies,id',
            'student_ids' => 'nullable|array'
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'status' => 1,
            ])->assignRole($request->role_id);

            if ($request->school_id) {
                $user->schools()->attach($request->school_id);
            }
            if ($request->department_id) {
                $user->schools()->attach($request->school_id);
                $user->departments()->attach($request->department_id);
            }
            if ($request->course_id) {
                $user->schools()->attach($request->school_id);
                $user->departments()->attach($request->department_id);
                $user->courses()->attach($request->course_id);
            }
            if ($request->company_id) {
                $user->companies()->attach($request->company_id);
            }
            if ($request->student_ids) {
                foreach ($request->student_ids as $studentId) {
                    DB::table('group_user')->insert([
                        'teacher_id' => $user->id,
                        'user_id' => $studentId,
                    ]);
                }
            }

            return redirect()->route('users.index')
                ->with('success', 'Berhasil menambahkan user baru');
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Gagal menambahkan user baru');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = decrypt($id);
        try {
            $user = User::find($id);
            return view('users.show', compact('user'));
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'User tidak ditemukan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = decrypt($id);
        try {
            $user = User::find($id);
            $isManager = auth()->user()->hasRole('manager');

            $isManager = auth()->user()->hasRole('manager');
            $isKaprog = auth()->user()->hasRole('kepala program');
            $isKabeng = auth()->user()->hasRole('kepala bengkel');

            if ($isManager) {
                $roles = Role::where('name', '!=', 'admin')->where('name', '!=', 'super-admin')->pluck('name', 'id');
                $schoolId = auth()->user()->schools()->first()->id;
                $schools = School::find($schoolId)->pluck('name', 'id');
                $departments = Department::where('school_id', $schoolId)->pluck('name', 'id');
                $courses = Course::join('departments', 'courses.department_id', '=', 'departments.id')
                    ->join('schools', 'departments.school_id', '=', 'schools.id')
                    ->where('schools.id', $schoolId)
                    ->pluck('courses.name', 'courses.id');
                $companies = Company::join('departments', 'companies.department_id', '=', 'departments.id')
                    ->join('schools', 'departments.school_id', '=', 'schools.id')
                    ->where('schools.id', $schoolId)
                    ->get();
                $modelIds = DB::table('model_has_roles')
                    ->where('role_id', 6)
                    ->pluck('model_id');
                $students = User::whereIn('id', $modelIds)->get();
            } elseif ($isKaprog) {
                $roles = Role::where('name', 'student')->orWhere('name', 'kepala program')->orWhere('name', 'mentor')->pluck('name', 'id');
                $schoolId = auth()->user()->schools()->first()->id;
                $schools = School::find($schoolId)->pluck('name', 'id');
                $departmentId = auth()->user()->departments()->first()->id;
                $departments = Department::where('id', $departmentId)->pluck('name', 'id');
                $courses = Course::where('department_id', $departmentId)->pluck('name', 'id');
                $companies = Company::where('department_id', $departmentId)->get();
                $modelIds = DB::table('model_has_roles')
                    ->where('role_id', 6)
                    ->pluck('model_id');
                $students = User::whereIn('id', $modelIds)->get();
            } elseif ($isKabeng) {
                $roles = Role::where('name', 'mentor')->pluck('name', 'id');
                $schoolId = auth()->user()->schools()->first()->id;
                $schools = School::find($schoolId)->pluck('name', 'id');
                $departmentId = auth()->user()->departments()->first()->id;
                $departments = Department::where('id', $departmentId)->pluck('name', 'id');
                $courses = Course::where('department_id', $departmentId)->pluck('name', 'id');
                $companies = Company::where('department_id', $departmentId)->get();
                $modelIds = DB::table('model_has_roles')
                    ->where('role_id', 6)
                    ->pluck('model_id');
                $students = User::whereIn('id', $modelIds)->get();
            } else {
                $roles = auth()->user()->hasRole('super-admin')
                    ? Role::pluck('name', 'id')
                    : Role::where('name', '!=', 'super-admin')->pluck('name', 'id');

                $schools = School::pluck('name', 'id');
                $departments = Department::pluck('name', 'id');
                $courses = Course::pluck('name', 'id');
                $companies = Company::all();
                $modelIds = DB::table('model_has_roles')
                    ->where('role_id', 6)
                    ->pluck('model_id');
                $students = User::whereIn('id', $modelIds)->get();
            }

            return view('users.edit', compact('user', 'schools', 'departments', 'courses', 'roles', 'companies', 'students'));
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'User tidak ditemukan');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $user = User::find($id);

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'same:confirm-password',
            'role_id' => 'required|exists:roles,id',
            'school_id' => 'nullable|exists:schools,id',
            'department_id' => 'nullable|exists:departments,id',
            'course_id' => 'nullable|exists:courses,id',
            'company_id' => 'nullable|exists:companies,id',
            'status' => 'boolean',
        ]);

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'status' => $request->status,
            ]);
            $user->syncRoles($request->role_id);

            if ($request->school_id) {
                $user->schools()->sync($request->school_id);
            }
            if ($request->department_id) {
                $user->departments()->sync($request->department_id);
            }
            if ($request->course_id) {
                $user->courses()->sync($request->course_id);
            }
            if ($request->company_id) {
                $user->companies()->sync($request->company_id);
            }

            return redirect()->route('users.index')
                ->with('success', 'User berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'User gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = decrypt($id);
        try {
            $user = User::find($id);
            $user->delete();
            return redirect()->route('users.index')
                ->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'User gagal dihapus');
        }
    }

    public function updateStatus(Request $request)
    {
        $id = decrypt($request->id);
        try {
            $user = User::findOrFail($id);

            if ($user->status == 1) {
                $user->update([
                    'status' => 0,
                ]);
                $state = 'dinonaktifkan';
            } else {
                $user->update([
                    'status' => 1,
                ]);
                $state = 'diaktifkan';
            }

            return back()->with('success', "User berhasil $state");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengubah status');
        }
    }

    public function editProfile()
    {
        $user = auth()->user();
        return view('users.edit-profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'gender' => 'nullable|in:male,female',
            'bio' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|min:10|max:20',
            'date_of_birth' => 'nullable|date',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = User::find($user->id);
        $oldEmail = $user->email;
        $user->update($request->all());

        if ($oldEmail != $request->email) {
            $user->email_verified_at = null;
            $user->save();
            // $user->sendEmailVerificationNotification();
        }

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = $user->id . time() . '_avatar.' . $avatar->getClientOriginalExtension();
            $avatar->move(storage_path('app/public/avatars'), $filename);
            $user->update([
                'avatar' => 'storage/avatars/' . $filename,
            ]);
        }

        return back()->with('success', 'Profil berhasil diubah');
    }
}
