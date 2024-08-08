<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Company;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;

class CompanyController extends Controller
{
    public function getData($school = null, $department = null, $search = null, $status = null, $sort = null)
    {
        $isAdmin = auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin');
        $isManager = auth()->user()->hasRole('manager');
        $isKaprog = auth()->user()->hasRole('kepala program');
        $isMentor = auth()->user()->hasRole('mentor');

        if (!$isMentor) {
            $school ??= auth()->user()?->schools()?->first()?->id;
            $department = $isKaprog
                ? auth()->user()->departments()->first()->id
                : $department;
        }

        $companies = Company::with(['vacancies' => function($query) {
            $query->withCount(['appliances as pending_appliances_count' => function($query) {
                $query->where('status', 'pending');
            }]);
        }]);

        if ($isMentor) {
            $companies = $companies->where('id', auth()->user()->companies()->first()->id);
        }
        if ($department) {
            $companies->where('department_id', $department);
        }
        if ($search) {
            $companies->search($search);
        }
        if ($status) {
            $companies->where('status', $status);
        }
        if ($sort) {
            if ($sort[0] == '-') {
                $sort = substr($sort, 1);
                $sortType = 'desc';
            } else {
                $sortType = 'asc';
            }
            $companies->orderBy($sort, $sortType);
        } else {
            $companies->orderBy('created_at', 'desc');
        }
        $companies = $companies->paginate(10);

        $companies->withPath('/companies')->withQueryString();

        $departments = $isAdmin
            ? Department::pluck('name', 'id')
            : Department::where('school_id', $school)->pluck('name', 'id');

        if ($companies->count() > 0) {
            $context = [
                'status' => true,
                'message' => 'Data perusahaan ditemukan',
                'companies' => $companies,
                'pagination' => $companies->links()->render(),
                'search' => $search,
                'statusData' => $status,
                'sort' => $sort,
                'selectedSchool' => $school,
                'departments' => $departments,
                'selectedDepartment' => $department,
            ];
        } else {
            $context = [
                'status' => false,
                'message' => 'Data perusahaan tidak ditemukan',
                'companies' => [],
                'pagination' => $companies->links()->render(),
                'search' => $search,
                'statusData' => $status,
                'sort' => $sort,
                'selectedSchool' => $school,
                'departments' => $departments,
                'selectedDepartment' => $department,
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
        $school = 1;
        $department = $request->query('department');
        $search = $request->query('search');
        $status = $request->query('status');
        $sort = $request->query('sort');

        $sort = $sort ? $sort : '-created_at';
        $context = $this->getData($school, $department, $search, $status, $sort);

        return view('companies.index', $context);
    }

    public function search(Request $request)
    {
        $school = $request->query('school');
        $department = $request->query('department');
        $search = $request->query('search');
        $status = $request->query('status');
        $sort = $request->query('sort');

        $context = $this->getData($school, $department, $search, $status, $sort);

        return view('companies.index', $context);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $isKaprog = auth()->user()->hasRole('kepala program');

        $departments = $isKaprog
            ? auth()->user()->departments()->get()
            : Department::all();

        return view('companies.create', compact('departments'));
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
            'category' => 'nullable',
            'address' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable',
            'contact_person' => 'nullable',
            'department_id' => 'required|exists:departments,id',
        ]);

        $company = Company::create([
            'name' => $request->name,
            'category' => $request->category,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'contact_person' => $request->contact_person,
            'department_id' => $request->department_id,
        ]);

        return redirect()->route('companies.index')->with('success', 'Data perusahaan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = decrypt($id);
        $company = Company::findOrFail($id);

        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $company = Company::findOrFail($id);
        $isKaprog = auth()->user()->hasRole('kepala program');

        $departments = $isKaprog
            ? auth()->user()->departments()->get()
            : Department::all();

        return view('companies.edit', compact('company', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $company = Company::findOrFail($id);

        $this->validate($request, [
            'name' => 'required',
            'category' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'contact_person' => 'required',
            'department_id' => 'required|exists:departments,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $company->update([
            'name' => $request->name,
            'category' => $request->category,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'contact_person' => $request->contact_person,
            'website' => $request->website,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'department_id' => $request->department_id,
        ]);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $filename = $company->id . time() . '.' . $logo->getClientOriginalExtension();
            $path = storage_path('app/public/companies/');
            $logo->move($path, $filename);

            $company->update([
                'logo' => '/storage/companies/' . $filename,
            ]);
        }

        return redirect()->route('companies.index')->with('success', 'Data perusahaan berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = decrypt($id);
        $company = Company::findOrFail($id);

        $company->delete();

        return redirect()->route('companies.index')->with('success', 'Data perusahaan berhasil dihapus');
    }
}
