<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pages = Page::latest();
        if (! empty($request->get('keyword'))) {
            $pages = $pages->where('name', 'like', '%'.$request->get('keyword').'%');
            // $pages = $pages->orwhere('email', 'like', '%'.$request->get('keyword').'%');
            // code...
        }
        $pages = $pages->paginate(10);

        return view('admin.pages.show', ['pages' => $pages]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:pages',
            'content' => 'required',
        ]);
        if ($validator->passes()) {

            $page = new Page;
            $page->name = $request->name;
            $page->slug = $request->slug;
            $page->content = $request->content;
            $page->save();
            session()->flash('success', 'Page added successfully.');

            return response()->json(
                [
                    'status' => true,
                    'massege' => 'Page added successfully.',

                ]);
        } else {
            return response()->json(
                [
                    'status' => false,
                    'errors' => $validator->errors(),

                ]);
        }
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
    public function edit(Request $request, $id)
    {
        $page = Page::find($id);
        if (empty($page)) {
            $request->session()->flash('error', 'Record not found');

            return redirect()->route('pages.index');
        }

        // $page = $page->get();

        return view('admin.pages.edit', ['page' => $page]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $page = Page::find($id);
        if (empty($page)) {
            $request->session()->flash('error', 'Page not found');

            return response()->json([
                'status' => true,
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            // 'slug' => 'required|slug|unique:pages,slug,'.$id.',id',
            'slug' => 'required',
        ]);
        if ($validator->passes()) {

            $page->name = $request->name;
            $page->slug = $request->slug;
            $page->content = $request->content;
            $page->save();
            session()->flash('success', 'Page updated successfully.');

            return response()->json(
                [
                    'status' => true,
                    'massege' => 'Page updated successfully.',

                ]);
        } else {
            return response()->json(
                [
                    'status' => false,
                    'errors' => $validator->errors(),

                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $page = Page::find($id);
        if (empty($page)) {
            $request->session()->flash('error', 'Page not found');

            return response()->json([
                'status' => true,
                'notFound' => true,
                // 'message' => 'User not found',
            ]);
        }
        $page->delete();
        $request->session()->flash('success', 'Page delete successfully');

        return response()->json([
            'status' => true,
            'message' => 'Page delete successfully',
        ]);
    }
}
