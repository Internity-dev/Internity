<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FaqController extends Controller
{
    public function getData($search = null, $status = null, $sort = null)
    {
        $faqs = Faq::when($search, function ($query, $search) {
            return $query->search($search);
        })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($sort, function ($query, $sort) {
                if ($sort[0] == '-') {
                    $sort = substr($sort, 1);
                    $sortType = 'desc';
                } else {
                    $sortType = 'asc';
                }
                return $query->orderBy($sort, $sortType);
            })
            ->paginate(10);

        $faqs->withPath('/faqs')->withQueryString();

        if ($faqs->count() > 0) {
            $context = [
                'status' => true,
                'message' => 'Data FAQ ditemukan',
                'faqs' => $faqs,
                'pagination' => $faqs->links()->render(),
                'search' => $search,
                'statusData' => $status,
                'sort' => $sort,
            ];
        } else {
            $context = [
                'status' => false,
                'message' => 'Data FAQ tidak ditemukan',
                'faqs' => [],
                'pagination' => $faqs->links()->render(),
                'search' => $search,
                'statusData' => $status,
                'sort' => $sort,
            ];
        }

        return $context;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $sort = $request->query('sort');

        $sort = $sort ? $sort : 'question';

        $context = $this->getData($search, $status, $sort);

        return view('faqs.index', $context);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('faqs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        $answer = $this->saveContentImages($request->answer);

        Faq::create([
            'question' => $request->question,
            'answer' => $answer,
        ]);

        return redirect()->route('faqs.index')->with('success', 'FAQ berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $id = decrypt($id);
        $faq = Faq::findOrFail($id);
        // dd($faq);
        return view('faqs.edit', compact('faq'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        $faq = Faq::find(decrypt($id));
        $oldAnswer = $faq->answer;

        $answer = $this->saveContentImages($request->answer);
        // dd($answer, $oldAnswer);

        $faq->update([
            'question' => $request->question,
            'answer' => $answer
        ]);

        $this->deleteUnusedImages($oldAnswer, $answer);

        return redirect()->route('faqs.index')->with('success', 'FAQ berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $faq = Faq::find(decrypt($id));
        $this->deleteImages($faq->answer);
        $faq->delete();

        return back()->with('success', 'Faq deleted successfully!');
    }

    private function saveContentImages($content)
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $images = $dom->getElementsByTagName('img');

        foreach ($images as $key => $img) {
            $src = $img->getAttribute('src');

            // Jika gambar adalah base64 (gambar baru diupload)
            if (strpos($src, 'data:image') === 0) {
                $data = base64_decode(explode(',', explode(';', $src)[1])[1]);
                $image_name = 'uploads/' . time() . $key . '.png';
                file_put_contents(public_path($image_name), $data);

                $img->removeAttribute('src');
                $img->setAttribute('src', env('ASSET_URL') . $image_name);
            }
        }

        return $dom->saveHTML();
    }


    private function deleteUnusedImages($oldAnswer, $used_images)
    {
        if (!is_array($used_images)) {
            $used_images = [$used_images];
        }
    
        $domain = env('ASSET_URL');
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($oldAnswer, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    
        $oldImages = $dom->getElementsByTagName('img');
        $old_image_paths = [];
    
        foreach ($oldImages as $img) {
            $old_image = $img->getAttribute('src');
            if (empty($old_image)) {
                continue;
            }
            $old_image_paths[] = $old_image;
        }
    
        $domUsedImages = new DOMDocument();
        $domUsedImages->loadHTML(implode("", $used_images), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $usedImagePaths = [];
    
        $usedImages = $domUsedImages->getElementsByTagName('img');
        foreach ($usedImages as $img) {
            $usedImagePaths[] = $img->getAttribute('src');
        }
    
        foreach ($old_image_paths as $old_image) {
            // dd(in_array($old_image, $usedImagePaths));
            if (!in_array($old_image, $usedImagePaths)) {
                if (strpos($old_image, $domain) === 0) {
                    $old_image = str_replace($domain, '', $old_image);
                }
                $old_image_path = public_path($old_image);
    
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
        }
    }
    
    private function deleteImages($answer)
    {
        $domain = env('ASSET_URL');
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($answer, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            $src = str_replace($domain, '', $src);
            $image_path = public_path($src);

            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
    }
}
