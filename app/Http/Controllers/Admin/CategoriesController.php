<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use Illuminate\Support\Facades\Input;

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Request()->get('q')){
            $categories = Category::where('title', 'like', '%'.Request()->get('q').'%')->paginate(10);
        }else{
            $categories = Category::orderBy('parent_id', 'ASC')->paginate(10);
        }

        return view('admin.categories.index', ['categories' => $categories->appends(Input::except('page'))])->with('categories', $categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::orderBy('title', 'asc')->pluck('title', 'id');
        return view('admin.categories.create')->with('categories', $categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:100',
            'blurb' => 'max:250',
            'slug' => 'required:max:100'
        ]);
        $category = new Category;
        $category->id = (int)$request->input('id');
        $category->parent_id = (int)$request->input('parent_id')??0;
        $category->title = $request->input('title');
        $category->slug = makeSlug($request->input('slug'));
        $category->blurb = $request->input('blurb');
        $category->total_products = 0;
        $category->is_featured = $request->input('is_featured');
        $category->save();
        return redirect('/admin/categories')->with('success', 'Category added.');
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
        $category = Category::find($id);
        $categories = Category::orderBy('title', 'asc')->pluck('title', 'id');
       // $categories = Category::with('childrenRecursive')->where('parent_id', 0)->pluck('title', 'id');
        return view('admin.categories.edit')->with(compact('category', $category, 'categories', $categories));
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
        $this->validate($request, [
            'title' => 'required|max:100',
            'blurb' => 'max:250',
            'slug' => 'required:max:100'
        ]);
        $category = Category::find($id);
        $category->parent_id = (int)$request->input('parent_id');
        $category->title = $request->input('title');
        $category->slug = makeSlug($request->input('slug'));
        $category->blurb = $request->input('blurb');
        $category->total_products = 0;
        $category->is_featured = $request->input('is_featured');

        $category->save();
        return redirect('/admin/categories')->with('success', 'Category updated.');
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

    /**
     * Categories CSV import
     */
    public function csvform()
    {
        return view('admin.categories.csv-form');
    }

    /**
     * Post process the csv file
     */
    public function csvStore(Request $request)
    {
        $this->validate($request,[
            'file' => 'required|mimes:csv,txt'
        ]);
        if($request->hasFile('file'))
        {
            //get the file extension
            $ext = $request->file('file')->getClientOriginalExtension();
            $filename_save = 'cats.'.$ext;
            $file = $request->file('file')->storeAs('public/categories', $filename_save);

            Category::truncate();
        }

        $file = $request->file('file');
        $handle = fopen($file, 'r');

        fgetcsv($handle, 0, ',');
        while (($data = fgetcsv($handle, 0, ',')) !== FALSE){
            Category::create([
                'id'                => (int) $data[0],
                'title'             => $data[1],
                'slug'              => makeSlug($data[1]),
                'blurb'             => $data[2],
                'parent_id'         => (int) $data[3],
                'total_products'    => 0,
            ]);
        }
        fclose($handle);
        return redirect('/admin/categories')->with('success', 'Categories successfully imported');
    }
}
