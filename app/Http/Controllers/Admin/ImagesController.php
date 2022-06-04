<?php

namespace App\Http\Controllers\Admin;

use App\Product;
use App\ProductImage;
use App\ProductImageLink;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

class ImagesController extends Controller
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
        $merchant = null;
        $images = ProductImageLink::where('is_downloaded', '0')->orderBy('id', 'DESC')->paginate(10);
        if(Request()->get('mId')){
            $mId = Request()->get('mId');
            $merchant = $mId;
            $images = ProductImageLink::where('merchant_id', $mId)->paginate(10);
        }
        return view('admin.images.index', ['images' => $images->appends(Input::except('page'))])->with(compact('merchant'));
    }

    /**
     * Download images one by one
     */
    public function download($id)
    {
        ini_set('max_execution_time', 0); //No limit
        $image = ProductImageLink::find($id);

        $local_path = 'storage/products/images/'.$image->product_id.'/';

        $ext = pathinfo($image->download_path, PATHINFO_EXTENSION);
        $download_path = substr($image->download_path, 0 , (strrpos($image->download_path, ".")));

        $ext = explode('?', $ext);
        $ext = rtrim($ext[0]);
        $download_path = $download_path.'.'.$ext;

        $dest = $local_path.$id.'.'.$ext;
        if(!is_dir($local_path)){
            mkdir($local_path, 0777, $download_path);
        }

        if(isset($download_path)){
            @copy($download_path, $dest);
        }

        $new_image = new ProductImage;
        $new_image->product_id = $image->product_id;
        $new_image->path = $dest;
        $new_image->is_default = '0';
        $new_image->save();

        $image->is_downloaded = '1';
        $image->save();

        return redirect()->back()->with('success', 'Product image has been downloaded');
    }

    /**
     * Download all the new images
     */
    public function downloadAll()
    {
        ini_set('max_execution_time', 0); //No limit
       $links = ProductImageLink::where('is_downloaded', '0')->get();

       if(Request()->get('mId')){
           $mId = Request()->get('mId');
           $links = ProductImageLink::where('merchant_id', $mId)->get();
       }

       foreach($links as $link){
            $this->download($link->id);
       }

        return redirect()->back()->with('success', 'Product images have been downloaded');
    }

        /**
     * Force download all the new images
     */
    public function forceDownloadAll()
    {
        ini_set('max_execution_time', 0); //No limit
       $links = ProductImageLink::all();

       foreach($links as $link){
            $this->download($link->id);
       }

        return redirect()->back()->with('success', 'Product images have been downloaded');
    }
}
