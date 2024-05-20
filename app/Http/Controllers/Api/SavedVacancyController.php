<?php

namespace App\Http\Controllers\Api;

use App\Models\SavedVacancy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Vacancy;

class SavedVacancyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->user()->id;
        try {
            $vacancies = SavedVacancy::where('user_id', $userId)->with('vacancy')->get();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error while getting vacancies',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Vacancies retrieved successfully',
            'vacancies' => $vacancies,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vacancy_id' => 'required|integer|exists:vacancies,id',
        ]);

        try {
            $vacancy = Vacancy::findOrFail($request->vacancy_id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error while getting vacancy',
                'error' => $e->getMessage(),
            ], 500);
        }

        try {
            $userDept = auth()->user()->departments()->first()->id;
            $vacancyDept = $vacancy->company->department->id;

            if (SavedVacancy::where('user_id', auth()->user()->id)->where('vacancy_id', $request->vacancy_id)->exists()) {
                return response()->json([
                    'message' => 'Anda sudah menyimpan lowongan ini',
                ], 403);
            }

            if ($userDept != $vacancyDept) {
                return response()->json([
                    'message' => 'Lowongan ini tidak sesuai dengan jurusan anda',
                ], 403);
            }

            $savedVacancy = SavedVacancy::create([
                'user_id' => auth()->user()->id,
                'vacancy_id' => $request->vacancy_id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error while saving vacancy',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Vacancy saved successfully',
            'vacancy' => $savedVacancy,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(SavedVacancy $savedVacancy)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SavedVacancy $savedVacancy)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SavedVacancy $savedVacancy)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $savedVacancy = SavedVacancy::where('user_id', auth()->user()->id)->where('vacancy_id', $id)->first();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error while getting saved vacancy',
                'error' => $e->getMessage(),
            ], 500);
        }

        try {
            $savedVacancy->delete();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error while unsaving vacancy',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Vacancy unsaved successfully',
        ], 200);
    }
}
