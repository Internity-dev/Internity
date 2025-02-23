<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function update(Request $request)
    {
        $userId = $request->user()->id;
        $user = User::find($userId);
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'address' => 'max:255',
            'phone' => 'nullable|min:10',
            'gender' => 'in:male,female',
            'date_of_birth' => 'date',
            'bio' => 'max:255',
            'skills' => 'nullable',
            'nis' => 'required|string|unique:users,nis,' . $user->id,
        ]);

        $user->update($request->all());

        return response()->json([
            'message' => 'Data berhasil diubah',
            'user' => $user
        ]);
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

    public function uploadAvatar(Request $request)
    {
        $userId = $request->user()->id;
        $user = User::find($userId);
        $file = $request->file('avatar');
        $filename = $file->getClientOriginalName();
        $path = $file->move(storage_path('app/public/avatars'), $filename);
        $user->avatar = 'storage/avatars/' . $filename;
        $user->save();
        return response()->json([
            'message' => 'Avatar berhasil diunggah',
            'avatar' => $user->avatar
        ]);
    }

    public function uploadResume(Request $request)
    {
        $userId = auth()->user()->id;
        $user = User::find($userId);
        $file = $request->file('resume');
        $filename = $file->getClientOriginalName();
        $path = $file->move(storage_path('app/public/resumes'), $filename);
        $user->resume = 'storage/resumes/' . $filename;
        $user->save();
        return response()->json([
            'message' => 'CV berhasil diunggah',
            'resume' => $user->resume
        ]);
    }
}
