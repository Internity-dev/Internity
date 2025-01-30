<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Score;
use Illuminate\Http\Request;
use App\Http\Requests\StoreScoreRequest;
use App\Http\Requests\UpdateScoreRequest;

class ScoreController extends Controller
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

        $scores = Score::where('user_id', $userId)->where('company_id', $companyId)->paginate(10);
        $scores->withPath('/scores?user=' . encrypt($userId) . '&company=' . encrypt($companyId));

        $userName = User::find($userId)->name;

        if ($scores->count() > 0) {
            $context = [
                'status' => true,
                'message' => 'Data score ditemukan',
                'scores' => $scores,
                'pagination' => $scores->links()->render(),
                'userName' => $userName,
                // 'search' => $search,
                // 'statusData' => $status,
                // 'sort' => $sort,
            ];
        } else {
            $context = [
                'status' => false,
                'message' => 'Data score tidak ditemukan',
                'scores' => [],
                'pagination' => $scores->links()->render(),
                'userName' => $userName,
                // 'search' => $search,
                // 'statusData' => $status,
                // 'sort' => $sort,
            ];
        }

        return view('scores.index', $context);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userId = $request->query('user');
        ! $userId ? abort(400, 'Missing user id') : $userId = decrypt($userId);

        $companyId = $request->query('company');
        ! $companyId ? abort(400, 'Missing company id') : $companyId = decrypt($companyId);

        $context = [
            'userId' => $userId,
            'companyId' => $companyId,
        ];

        return view('scores.create', $context);
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
            'user_id' => 'required|exists:users,id',
            'company_id' => 'required|exists:companies,id',
            'name' => 'required',
            'score' => 'required|integer',
            'type' => 'required|in:teknis,non-teknis'
        ]);

        Score::create($request->all());

        return redirect()->route('scores.index', ['user' => encrypt($request->user_id), 'company' => encrypt($request->company_id)]);
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
        $score = Score::find($id);

        if ($score) {
            $context = [
                'status' => true,
                'message' => 'Data score ditemukan',
                'score' => $score,
            ];
        } else {
            $context = [
                'status' => false,
                'message' => 'Data score tidak ditemukan',
                'score' => [],
            ];
        }

        return view('scores.show', $context);
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
        $score = Score::find($id);

        if ($score) {
            $context = [
                'status' => true,
                'message' => 'Data score ditemukan',
                'score' => $score,
            ];
        } else {
            $context = [
                'status' => false,
                'message' => 'Data score tidak ditemukan',
                'score' => [],
            ];
        }

        return view('scores.edit', $context);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $request->validate([
            'name' => 'required',
            'score' => 'required|integer',
            'type' => 'required|in:teknis,non-teknis'
        ]);

        $score = Score::find($id);
        $score->update([
            'name' => $request->name,
            'score' => $request->score,
            'type' => $request->type,
        ]);

        return redirect()->route('scores.index', ['user' => encrypt($score->user_id), 'company' => encrypt($score->company_id)]);
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
        $score = Score::find($id);
        $score->delete();

        return redirect()->route('scores.index', ['user' => encrypt($score->user_id), 'company' => encrypt($score->company_id)]);
    }
}
