<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Course;
use App\Models\School;
use App\Models\Department;
use Illuminate\Http\Request;

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
        $isManager = auth()->user()->hasRole('manager');
        $isTeacher = auth()->user()->hasRole('teacher');

        $users = User::when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($school, function ($query, $school) {
                return $query->whereHas('schools', function($query) use ($school) {
                    $query->where('school_id', $school);
                });
            })
            ->when($department, function ($query, $department) {
                return $query->whereHas('departments', function($query) use ($department) {
                    $query->where('department_id', $department);
                });
            })
            ->when($isManager, function ($query) {
                return $query->manager(auth()->user()->schools()->first()->id);
            })
            ->when($isTeacher, function ($query) {
                return $query->teacher(auth()->user()->departments()->first()->id);
            })
            ->when($sort, function ($query, $sort) {
                if ($sort[0] == '-') {
                    $sort = substr($sort, 1);
                    $sortType = 'desc';
                } else {
                    $sortType = 'asc';
                }

                return $query->orderBy($sort, $sortType);
            })
            ->paginate(10);

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

        if ($isManager) {
            $roles = Role::where('name', '!=', 'admin')->where('name', '!=', 'super-admin')->pluck('name', 'id');
            $schoolId = auth()->user()->schools()->first()->id;
            $schools = School::find($schoolId)->pluck('name', 'id');
            $departments = Department::where('school_id', $schoolId)->pluck('name', 'id');
            $courses = Course::join('departments', 'courses.department_id', '=', 'departments.id')
                ->join('schools', 'departments.school_id', '=', 'schools.id')
                ->where('schools.id', $schoolId)
                ->pluck('courses.name', 'courses.id');

        } else {
            $roles = auth()->user()->hasRole('super-admin')
                ? Role::pluck('name', 'id')
                : Role::where('name', '!=', 'super-admin')->pluck('name', 'id');

            $schools = School::pluck('name', 'id');
            $departments = Department::pluck('name', 'id');
            $courses = Course::pluck('name', 'id');
        }

        return view('users.create', compact('schools', 'departments', 'courses', 'roles'));
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
            'school_id' => 'exists:schools,id',
            'department_id' => 'exists:departments,id',
            'course_id' => 'exists:courses,id',
            'status' => 'boolean'
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'status' => $request->status,
            ])->assignRole($request->role_id);

            if ($request->school_id) {
                $user->schools()->attach($request->school_id);
            }
            if ($request->department_id) {
                $user->departments()->attach($request->school_id);
                $user->departments()->attach($request->department_id);
            }
            if ($request->course_id) {
                $user->courses()->attach($request->school_id);
                $user->courses()->attach($request->department_id);
                $user->courses()->attach($request->course_id);
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

            if ($isManager) {
                $roles = Role::where('name', '!=', 'admin')->where('name', '!=', 'super-admin')->pluck('name', 'id');
                $schoolId = auth()->user()->schools()->first()->id;
                $schools = School::find($schoolId)->pluck('name', 'id');
                $departments = Department::where('school_id', $schoolId)->pluck('name', 'id');
                $courses = Course::join('deparments', 'courses.department_id', '=', 'departments.id')
                    ->join('schools', 'departments.school_id', '=', 'schools.id')
                    ->where('schools.id', $schoolId)
                    ->pluck('courses.name', 'courses.id');
            } else {
                $roles = auth()->user()->hasRole('super-admin')
                ? Role::pluck('name', 'id')
                : Role::where('name', '!=', 'super-admin')->pluck('name', 'id');

                $schools = School::pluck('name', 'id');
                $departments = Department::pluck('name', 'id');
                $courses = Course::pluck('name', 'id');
            }

            return view('users.edit', compact('user', 'schools', 'departments', 'courses'));
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
            'school_id' => 'exists:schools,id',
            'department_id' => 'exists:departments,id',
            'course_id' => 'exists:courses,id',
            'status' => 'boolean',
            'bio' => 'string|max:255',
            'address' => 'string|max:255',
            'phone' => 'string',
            'date_of_birth' => 'date',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'status' => $request->status,
                'bio' => $request->bio,
                'address' => $request->address,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
            ])->syncRoles($request->role_id);

            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $name = $request->name . time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = storage_path('app/public/avatars');
                $image->move($destinationPath, $name);
                $user->update([
                    'avatar' => $destinationPath . '/' . $name,
                ]);
            }
            if ($request->school_id) {
                $user->schools()->sync($request->school_id);
            }
            if ($request->department_id) {
                $user->departments()->sync($request->department_id);
            }
            if ($request->course_id) {
                $user->courses()->sync($request->course_id);
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
            $user = User::find($id);

            if ($user->status == 1) {
                $user->status = 0;
                $state = 'dinonaktifkan';
            } else {
                $user->status = 1;
                $state = 'diaktifkan';
            }

            return back()->with('success', "User berhasil $state");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengubah status');
        }
    }
}
