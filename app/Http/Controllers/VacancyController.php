<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use Illuminate\Http\Request;
use App\Http\Requests\StoreVacancyRequest;
use App\Http\Requests\UpdateVacancyRequest;

class VacancyController extends Controller
{
    private function getData($company, $search=null, $sort=null)
    {
        $vacancies = Vacancy::query()->withCount(['appliances as pending_appliances_count' => function($query) {
                $query->where('status', 'pending');
            }])
            ->company($company)
            ->when($search, function ($query, $search) {
                return $query->search($search);
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

        $vacancies->withPath('/vacancies')->withQueryString();

        if ($vacancies->count() > 0) {
            $context = [
                'status' => true,
                'message' => 'Data lowongan ditemukan',
                'vacancies' => $vacancies,
                'company' => $company,
            ];
        } else {
            $context = [
                'status' => false,
                'message' => 'Data lowongan tidak ditemukan',
                'vacancies' => $vacancies,
                'company' => $company,
            ];
        }

        return $context;
    }

    /**
     * Display a listing of the resource.
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $companyId = $request->query('company');
        ! $companyId ? abort(404) : $companyId = decrypt($companyId);

        $search = $request->query('search');
        $sort = $request->query('sort');

        $context = $this->getData($companyId, $search, $sort);

        return view('vacancies.index', $context);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $companyId = $request->query('company');
        ! $companyId ? abort(404) : $companyId = decrypt($companyId);

        return view('vacancies.create', compact('companyId'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'slots' => 'required|integer',
        ]);

        $vacancy = Vacancy::create($request->all());

        if ($vacancy) {
            return redirect()->route('vacancies.index', ['company' => encrypt($request->company_id)])->with('success', 'Lowongan berhasil ditambahkan');
        } else {
            return redirect()->route('vacancies.index', ['company' => encrypt($request->company_id)])->with('error', 'Lowongan gagal ditambahkan');
        }
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
        $vacancy = Vacancy::findOrFail($id);

        return view('vacancies.show', compact('vacancy'));
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
        $vacancy = Vacancy::findOrFail($id);

        return view('vacancies.edit', compact('vacancy'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'skills' => 'required|string',
            'slots' => 'required|integer',
        ]);

        $vacancy = Vacancy::findOrFail($id);
        $vacancy->update($request->all());

        return redirect()->route('vacancies.index', ['company' => encrypt($vacancy->company_id)])->with('success', 'Lowongan berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vacancy  $vacancy
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = decrypt($id);
        $vacancy = Vacancy::findOrFail($id);
        $vacancy->delete();

        return back()->with('success', 'Lowongan berhasil dihapus');
    }
}
