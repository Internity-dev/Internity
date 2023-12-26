<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Vacancy;
use App\Models\Appliance;
use Illuminate\Http\Request;
use App\Models\PresenceStatus;
use App\Http\Controllers\Controller;

class ApplianceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = auth()->user()->id;
        try {
            $appliances = Appliance::where('user_id', $userId)->with('vacancy')->get();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error while getting appliances',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Appliances retrieved successfully',
            'appliances' => $appliances,
        ], 200);
    }

    public function accepted()
    {
        $userId = auth()->user()->id;
        try {
            $appliances = Appliance::where('user_id', $userId)
                ->where('status', 'accepted')
                ->get()->toArray();

            $internDates = auth()->user()->internDates()->get();

            foreach ($appliances as $key => $appliance) {
                $vacancy = Vacancy::find($appliance['vacancy_id']);
                $appliances[$key]['vacancy'] = $vacancy;
                $appliances[$key]['intern_date'] = $internDates[$key];
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error while getting appliances',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Appliances retrieved successfully',
            'appliances' => $appliances,
            'intern_date' => $internDates
        ], 200);
    }

    public function editDate(Request $request, $id)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'extend' => 'nullable|integer',
        ]);

        $user = auth()->user();

        try {
            $user->internDates()->where('company_id', $id)->update($request->all());

            $presencePending = PresenceStatus::where('name', 'Pending')->first('id')->id;

            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            for ($i = $startDate; $i <= $endDate; $i->addDay()) {
                $presence = $user->presences()->where('company_id', $id)->where('date', $i)->first();
                $journal = $user->journals()->where('company_id', $id)->where('date', $i)->first();

                if (! $presence) {
                    $user->presences()->create([
                        'company_id' => $id,
                        'date' => $i,
                        'presence_status_id' => $presencePending,
                    ]);
                }
                if (! $journal) {
                    $user->journals()->create([
                        'company_id' => $id,
                        'date' => $i,
                    ]);
                }
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error while updating date',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Date updated successfully',
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

            if (Appliance::where('user_id', auth()->user()->id)->where('vacancy_id', $request->vacancy_id)->whereIn('status', ['pending', 'accepted', 'processed'])->exists()) {
                return response()->json([
                    'message' => 'Anda sudah mendaftar pada lowongan ini',
                ], 403);
            }

            if ($userDept != $vacancyDept) {
                return response()->json([
                    'message' => 'Lowongan ini tidak sesuai dengan jurusan anda',
                ], 403);
            }

            $appliance = Appliance::create([
                'user_id' => auth()->user()->id,
                'vacancy_id' => $request->vacancy_id,
                'status' => 'pending',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error while creating appliance',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Appliance created successfully',
            'appliance' => $appliance,
        ], 201);
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
            $appliance = Appliance::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error while getting appliance',
                'error' => $e->getMessage(),
            ], 500);
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

    public function cancel($id)
    {
        try {
            $appliance = Appliance::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error while getting appliance',
                'error' => $e->getMessage(),
            ], 500);
        }

        try {
            $appliance->update([
                'status' => 'canceled',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error while canceling appliance',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Appliance canceled successfully',
            'appliance' => $appliance,
        ], 200);
    }
}
