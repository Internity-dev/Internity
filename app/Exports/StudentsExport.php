<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use App\Models\User;

class StudentsExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $search = $this->request->query('search');
        $status = $this->request->query('status');
        $school = $this->request->query('school');
        $department = $this->request->query('department');
        $sort = $this->request->query('sort');
        $sort = $sort ? $sort : 'name';

        // Fetch the data using the actual logic
        $context = $this->getData($search, $status, $school, $department, $sort);

        // Map the data to match the structure expected by the export
        $data = $context['students']->map(function ($user) {
            return [
                'nama' => $user->name,
                'kelas' => $user->courses()->first()?->name ?? 'N/A', // Adjust based on your actual data
            ];
        });

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Kelas'
        ];
    }

    // Assuming you have this method available
    private function getData($search = null, $status = null, $school = null, $department = null, $sort = null)
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
        if ($isTeacher) {
            $users = $users->teacher(auth()->user()->departments()->first()->id);
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
