<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Http\Request;
use App\Models\User;

class StudentsExport implements WithMultipleSheets
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function sheets(): array
    {
        $search = $this->request->query('search');
        $status = $this->request->query('status');
        $school = $this->request->query('school');
        $department = $this->request->query('department');
        $sort = $this->request->query('sort');
        $sort = $sort ? $sort : 'name';

        $context = $this->getData($search, $status, $school, $department, $sort);

        // Group students by 'kelas'
        $grouped = $context['students']->groupBy(function ($user) {
            return $user->courses()->first()?->name ?? 'N/A';
        });

        $sheets = [];

        foreach ($grouped as $kelas => $students) {
            $sheets[] = new StudentsPerKelasSheet($kelas, $students);
        }

        return $sheets;
    }

    // Assuming you have this method available
    private function getData($search = null, $status = null, $school = null, $department = null, $sort = null)
    {
        $isManager = auth()->user()->hasRole('manager');
        $isKaprog = auth()->user()->hasRole('kepala program');
        $isKabeng = auth()->user()->hasRole('kepala bengkel');
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
            $users = $users->whereHas('schools', function ($query) use ($school) {
                $query->where('school_id', $school);
            });
        }
        if ($department) {
            $users = $users->whereHas('departments', function ($query) use ($department) {
                $query->where('department_id', $department);
            });
        }
        if ($isManager) {
            $users = $users->whereHas('schools', function ($query) {
                $query->where('school_id', auth()->user()->schools()->first()->id);
            });
        }
        if ($isKaprog || $isKabeng) {
            $users = $users->kaprog(auth()->user()->departments()->first()->id);
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
        $users = $users->get(); // Use get() instead of paginate() for export

        if ($users->count() > 0) {
            $context = [
                'status' => true,
                'message' => 'Data siswa ditemukan',
                'students' => $users,
            ];
        } else {
            $context = [
                'status' => false,
                'message' => 'Data siswa tidak ditemukan',
                'students' => collect([]),
            ];
        }

        return $context;
    }
}
