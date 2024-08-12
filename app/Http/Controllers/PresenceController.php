<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Presence;
use Illuminate\Http\Request;
use App\Http\Requests\StorePresenceRequest;
use App\Http\Requests\UpdatePresenceRequest;

class PresenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userId = $request->query('user');
        ! $userId ? abort(400, 'Missing user id') : $userId = decrypt($userId);

        $companyId = $request->query('company');
        ! $companyId ? abort(400, 'Missing company id') : $companyId = decrypt($companyId);

        $presences = Presence::where('user_id', $userId)->where('company_id', $companyId)->orderBy('date', 'asc')->paginate(35);
        $presences->withPath('/presences')->withQueryString();

        if ($presences->count() > 0) {
            $context = [
                'status' => true,
                'message' => 'Data kehadiran ditemukan',
                'presences' => $presences,
                'pagination' => $presences->links()->render(),
                'userName' => User::find($userId)->name,
                // 'search' => $search,
                // 'statusData' => $status,
                // 'sort' => $sort,
            ];
        } else {
            $context = [
                'status' => false,
                'message' => 'Data kehadiran tidak ditemukan',
                'presences' => [],
                'pagination' => $presences->links()->render(),
                'userName' => User::find($userId)->name,
                // 'search' => $search,
                // 'statusData' => $status,
                // 'sort' => $sort,
            ];
        }

        return view('presences.index', $context);
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
     * @param  \App\Http\Requests\StorePresenceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePresenceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Presence  $presence
     * @return \Illuminate\Http\Response
     */
    public function show(Presence $presence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Presence  $presence
     * @return \Illuminate\Http\Response
     */
    public function edit(Presence $presence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePresenceRequest  $request
     * @param  \App\Models\Presence  $presence
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePresenceRequest $request, Presence $presence)
    {
        //
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
        $presence = Presence::find($id);
        $presence->delete();

        return redirect()->back()->with('success', 'Data kehadiran berhasil dihapus');
    }

    public function approve($id)
    {
        $id = decrypt($id);
        $presence = Presence::find($id);
        $presence->update([
            'is_approved' => true,
        ]);

        return redirect()->back()->with('success', 'Data kehadiran berhasil disetujui');
    }
}
