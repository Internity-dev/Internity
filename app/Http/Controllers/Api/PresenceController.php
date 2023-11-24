<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Presence;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PresenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $company = $request->query('company');
        if (! $company) {
            return response()->json([
                'status' => false,
                'message' => 'Company ID is required',
            ], 400);
        }

        try {
            $user = auth()->user();

            $presences = $user->presences()->where('company_id', $company)->whereDate('date', '<=', Carbon::now())->orderBy('date', 'desc')->get();

            return response()->json([
                'status' => true,
                'message' => 'Presences retrieved successfully',
                'presences' => $presences,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error while getting presences',
                'error' => $e->getMessage(),
            ], 500);
        }
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
            $presence = Presence::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Presence retrieved successfully',
                'presence' => $presence,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error while getting presence',
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
        $request->validate([
            'check_in' => 'required_without:check_out|date_format:H:i:s',
            'check_out' => 'required_without:check_in|date_format:H:i:s',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string|max:255',
            'presence_status_id' => 'required|exists:presence_statuses,id',
        ]);

        try {
            $presence = Presence::findOrFail($id);

            $presence->update($request->all());

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $name = $presence->id . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->move(storage_path('app/public/presences'), $name);
                $presence->attachment = 'storage/presences/' . $name;
            }

            $presence->save();

            return response()->json([
                'status' => true,
                'message' => 'Presence updated successfully',
                'presence' => $presence,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error while updating presence',
                'error' => $e->getMessage(),
            ], 500);
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
        try {
            $presence = Presence::findOrFail($id);
            $presence->delete();

            return response()->json([
                'status' => true,
                'message' => 'Presence deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error while deleting presence',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function todayActivity(Request $request)
    {
        $company = $request->query('company');
        if (! $company) {
            return response()->json([
                'status' => false,
                'message' => 'Company ID is required',
            ], 400);
        }

        try {
            $user = auth()->user();

            $presence = $user->presences()->where('company_id', $company)->whereDate('date', Carbon::now())->whereHas('presenceStatus', function ($query) {
                $query->where('name', 'Pending');
            })->first();
            $journal = $user->journals()->where('company_id', $company)->whereDate('date', Carbon::now())->whereNull('work_type')->whereNull('description')->first();
            return response()->json([
                'status' => true,
                'message' => 'Presence retrieved successfully',
                'presence' => $presence,
                'journal' => $journal,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error while getting presence',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
