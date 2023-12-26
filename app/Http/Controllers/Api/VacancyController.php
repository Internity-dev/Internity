<?php

namespace App\Http\Controllers\Api;

use App\Models\Vacancy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VacancyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departmentId = auth()->user()->departments()->first()->id;
        try {
            $vacancies = Vacancy::whereHas('company', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->orderBy('updated_at', 'desc')->get();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data lowongan tidak ditemukan',
                'vacancies' => [],
            ], 404);
        }

        return response()->json([
            'status' => true,
            'count' => $vacancies->count(),
            'message' => 'Data lowongan ditemukan',
            'vacancies' => $vacancies,
        ], 200);
    }

    public function recommended()
    {
        $departmentId = auth()->user()->departments()->first()->id;
        
        try {
            $userSkills = explode(',', auth()->user()->skills);

            $vacancies = [];
            foreach ($userSkills as $skill) {
                $vacancy = Vacancy::search($skill)->query(function ($query) use ($departmentId) {
                    $query->whereHas('company', function ($query) use ($departmentId) {
                        $query->where('department_id', $departmentId);
                    });
                })->get();

                $vacancies = array_merge($vacancies, $vacancy->toArray());
            }
            $vacancies = collect($vacancies)->unique('id')->values()->all();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data lowongan tidak ditemukan',
                'vacancies' => [],
            ], 404);
        }

        return response()->json([
            'status' => true,
            'count' => count($vacancies),
            'message' => 'Data lowongan ditemukan',
            'vacancies' => $vacancies,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $vacancy = Vacancy::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data lowongan tidak ditemukan',
                'vacancy' => [],
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data lowongan ditemukan',
            'vacancy' => $vacancy,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
