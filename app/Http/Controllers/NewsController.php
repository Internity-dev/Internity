<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\School;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $category = $request->query('category');
        $schoolId = $request->query('school');
        $departmentId = $request->query('department');
        ! $schoolId ? abort(404) : $schoolId = decrypt($schoolId);

        if ($category == 'department') {
            if (! $departmentId) {
                if (auth()->user()->hasRole('kepala program')) {
                    $departmentId = auth()->user()->departments()->first()->id;
                } else {
                    $departmentId = Department::where('school_id', $schoolId)->first()->id;
                }
            } else {
                $departmentId = decrypt($departmentId);
            }
        }

        $acceptedCategory = ['school', 'department'];

        $schoolId = $category == 'school' ? auth()->user()->schools()->first()->id : $schoolId;
        ! in_array($category, $acceptedCategory) ? abort(404) : $category = 'App\\Models\\' . ucfirst($category);

        $news = News::where('newsable_type', $category)
            ->where('newsable_id', $category == 'App\\Models\\School' ? $schoolId : $departmentId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $news->withPath('/news')->withQueryString();

        $isKaprog = auth()->user()->hasRole('kepala program');
        $departments = $isKaprog
            ? auth()->user()->departments()->pluck('name', 'id')
            : Department::where('school_id', $schoolId)->pluck('name', 'id');

        $context = [
            'news' => $news,
            'departments' => $departments,
            'selectedSchool' => School::find($schoolId),
            'selectedDepartment' => Department::find($departmentId),
            'category' => $category == 'App\\Models\\School' ? 'school' : 'department',
        ];

        return view('news.index', $context);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $schoolId = $request->query('school');
        ! $schoolId ? abort(404) : $schoolId = decrypt($schoolId);
        $category = $request->query('category');
        $departmentId = $request->query('department');
        $acceptedCategory = ['school', 'department'];

        if ($category == 'department') {
            if (! $departmentId) {
                if (auth()->user()->hasRole('kepala program')) {
                    $departmentId = auth()->user()->departments()->first()->id;
                } else {
                    $departmentId = Department::where('school_id', $schoolId)->first()->id;
                }
            } else {
                $departmentId = decrypt($departmentId);
            }
        }

        ! in_array($category, $acceptedCategory) ? abort(404) : $category = 'App\\Models\\' . ucfirst($category);

        $departments = auth()->user()->hasRole('kepala program')
            ? auth()->user()->departments()->pluck('name', 'id')
            : Department::where('school_id', $schoolId)->pluck('name', 'id');

        $context = [
            'departments' => $departments,
            'selectedSchool' => School::find($schoolId),
            'selectedDepartment' => Department::find($departmentId),
            'category' => $category == 'App\\Models\\School' ? 'school' : 'department',
        ];

        return view('news.create', $context);
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
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'content' => 'required|string',
            'newsable_type' => 'required|string',
            'newsable_id' => 'required|integer',
        ]);

        $news = News::create([
            'title' => $request->title,
            'content' => $request->content,
            'newsable_type' => $request->newsable_type,
            'newsable_id' => $request->newsable_id,
            'user_id' => auth()->user()->id,
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(storage_path('app/public/news'), $imageName);
            $news->update(['image' => 'storage/news/' . $imageName]);
        }

        return redirect()->route('news.index', [
            'category' => $request->newsable_type == 'App\\Models\\School' ? 'school' : 'department',
            'school' => encrypt(1),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $news = News::find(decrypt($id));

        return view('news.show', compact('news'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $news = News::find(decrypt($id));

        return view('news.edit', compact('news'));
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
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'content' => 'required|string',
        ]);

        $news = News::find(decrypt($id));
        $news->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(storage_path('app/public/news'), $imageName);
            $news->update(['image' => 'storage/news/' . $imageName]);
        }

        return redirect()->route('news.index', [
            'category' => $news->newsable_type == 'App\\Models\\School' ? 'school' : 'department',
            'school' => encrypt(1),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $news = News::find(decrypt($id));
        $news->delete();

        return back()->with('success', 'News deleted successfully!');
    }
}
