<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        $notifications = Notification::where('user_id', $user->id)->orWhere('user_id', null)->orderBy('created_at', 'desc')->get();

        if ($notifications->count() > 0) {
            $context = [
                'status' => true,
                'message' => 'Data notifikasi ditemukan',
                'notifications' => $notifications,
            ];

            return response()->json($context, 200);
        } else {
            $context = [
                'status' => false,
                'message' => 'Tidak ada notifikasi untuk kamu',
                'notifications' => [],
            ];

            return response()->json($context, 404);
        }
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);
        $user = auth()->user();

        foreach ($request->ids as $id) {
            $user->notifications()->where('id', $id)->update(['read_at' => now()]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Notifikasi berhasil ditandai sebagai telah dibaca',
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
        //
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
