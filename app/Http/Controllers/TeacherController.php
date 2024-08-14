<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\PresenceStatus;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function getData($search=null, $status=null, $school=null, $department=null, $sort=null)
    {
        $isManager = auth()->user()->hasRole('manager');
        $isKaprog = auth()->user()->hasRole('kepala program');
        $isKabeng = auth()->user()->hasRole('kepala bengkel');
        $isTeacher = auth()->user()->hasRole('teacher');
        $isMentor = auth()->user()->hasRole('mentor');

        $users = User::whereRelation('roles', 'name', 'teacher');
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
                $users = $users->kabeng();
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
                'message' => 'Data guru ditemukan',
                'teachers' => $users,
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
                'message' => 'Data guru tidak ditemukan',
                'teachers' => [],
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
        ? view('teachers.index', $context)
        : view('teachers.index', $context)->with('error', $context['message']);
    }

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);

        
        $user = User::whereRelation('roles', 'name', 'teacher')
                    ->where('id', $id)
            ->first();

        $modelIds = DB::table('model_has_roles')
                    ->where('role_id', 6)
                    ->pluck('model_id');
        $students = User::whereIn('id', $modelIds)->get();
        $selectedStudentIds = DB::table('group_user')
                ->where('teacher_id', $id)
                ->pluck('user_id')
                ->toArray();

        if ($user) {

            $context = [
                'status' => true,
                'message' => 'Data guru ditemukan',
                'teacher' => $user,
                'students' => $students,
                'selectedStudentIds' => $selectedStudentIds
            ];
        } else {
            $context = [
                'status' => false,
                'message' => 'Data guru tidak ditemukan',
                'teacher' => null,
                'students' => null,
                'selectedStudentIds' => null
            ];
        }

        return view('teachers.edit', $context);
    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'student_ids' => 'nullable|array'
        ]);

        $user = User::whereRelation('roles', 'name', 'teacher')
            ->where('id', $id)
            ->first();

        if ($user) {
            $user->update([
                'name' => $request->name,
            ]);
            if ($request->student_ids) {
                DB::table('group_user')->where('teacher_id', $id)->delete();
                foreach ($request->student_ids as $studentId) {
                    DB::table('group_user')->insert([
                        'teacher_id' => $user->id,
                        'user_id' => $studentId,
                    ]);
                }
            }

            return redirect()->route('teachers.index')->with('success', 'Data guru berhasil diperbarui');
        } else {
            return redirect()->route('teachers.index')->with('error', 'Data guru tidak ditemukan');
        }
    }
}
