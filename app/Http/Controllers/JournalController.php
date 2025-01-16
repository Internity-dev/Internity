<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreJournalRequest;
use App\Http\Requests\UpdateJournalRequest;

class JournalController extends Controller
{
    public function getData($userId, $companyId, $search = null, $status = null, $sort = null, $paginate = true)
    {
        $journals = Journal::where('user_id', $userId)->where('company_id', $companyId);
        if ($search) {
            $journals = $journals->search($search);
        }
        if ($status) {
            $journals = $journals->where('status', $status);
        }
        if ($sort) {
            if ($sort[0] == '-') {
                $sort = substr($sort, 1);
                $sortType = 'desc';
            } else {
                $sortType = 'asc';
            }
            $journals = $journals->orderBy($sort, $sortType);
        } else {
            $journals = $journals->orderBy('date', 'desc');
        }
        if ($paginate) {
            $journals = $journals->paginate(10);
            $journals->withPath('/journals')->withQueryString();
        } else {
            $journals = $journals->get();
        }

        if ($journals->count() > 0) {
            $context = [
                'status' => true,
                'message' => 'Data jurnal ditemukan',
                'journals' => $journals,
                'pagination' => $journals->links()->render(),
                'search' => $search,
                'statusData' => $status,
                'sort' => $sort,
            ];
        } else {
            $context = [
                'status' => false,
                'message' => 'Data jurnal tidak ditemukan',
                'journals' => [],
                'pagination' => $journals->links()->render(),
                'search' => $search,
                'statusData' => $status,
                'sort' => $sort,
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
        $userId = $request->query('user');
        !$userId ? abort(404) : $userId = decrypt($userId);

        $companyId = $request->query('company');
        ! $companyId ? abort(404) : $companyId = decrypt($companyId);

        $search = $request->query('search');
        $status = $request->query('status');
        $sort = $request->query('sort');
        $userName = User::find($userId)->name;

        $context = $this->getData($userId,$companyId, $search, $status, $sort);
        $context['userName'] = $userName;
        return view('journals.index', $context);
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
     * @param  \App\Http\Requests\StoreJournalRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreJournalRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $journal = Journal::find(decrypt($id));
        if ($journal) {
            $context = [
                'status' => true,
                'message' => 'Data jurnal ditemukan',
                'journal' => $journal,
            ];
        } else {
            $context = [
                'status' => false,
                'message' => 'Data jurnal tidak ditemukan',
                'journal' => null,
            ];
        }

        return view('journals.show', $context);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $journal = Journal::find(decrypt($id));
        if ($journal) {
            $context = [
                'status' => true,
                'message' => 'Data jurnal ditemukan',
                'journal' => $journal,
            ];
        } else {
            $context = [
                'status' => false,
                'message' => 'Data jurnal tidak ditemukan',
                'journal' => null,
            ];
        }

        return view('journals.edit', $context);
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
        $request->validate([
            'date' => 'required|date',
            'work_type' => 'required|string',
            'description' => 'required|string',
        ]);

        $journal = Journal::find(decrypt($id));
        $journal->update([
            'date' => $request->date,
            'work_type' => $request->work_type,
            'description' => $request->description,
        ]);

        return redirect()->route('journals.index', ['user' => encrypt($journal->user_id), 'company' => encrypt($journal->company_id)])->with('success', 'Data jurnal berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $journal = Journal::find(decrypt($id));
        if ($journal) {
            $journal->delete();
            $context = [
                'status' => true,
                'message' => 'Data jurnal berhasil dihapus',
                'journal' => $journal,
            ];
        } else {
            $context = [
                'status' => false,
                'message' => 'Data jurnal tidak ditemukan',
                'journal' => null,
            ];
        }

        return back()->with($context);
    }

    public function approve($id)
    {
        $journal = Journal::find(decrypt($id));
        if ($journal) {
            $journal->update(['is_approved' => true]);
            $context = [
                'status' => true,
                'message' => 'Data jurnal berhasil diubah',
                'journal' => $journal,
            ];
        } else {
            $context = [
                'status' => false,
                'message' => 'Data jurnal tidak ditemukan',
                'journal' => null,
            ];
        }

        return back()->with($context);
    }

    public function bulkApprove(Request $request)
    {
        $userId = $request->query('user');
        $companyId = $request->query('company');

        $journals = Journal::where('user_id', $userId)
            ->where('company_id', $companyId)
            ->whereNotNull('work_type')
            ->whereNotNull('description')
            ->where('is_approved', false)
            ->update(['is_approved' => true]);

        return back()->with('success', 'All valid journals have been approved.');
    }
}
