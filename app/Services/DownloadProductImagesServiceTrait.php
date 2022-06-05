<?php

namespace App\Services;

use App\ProductImage;
use App\ProductImageLink;

/**
 * Class DownloadProductImagesService.
 */
trait DownloadProductImagesServiceTrait
{
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

    }

    protected function download($id)
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
    }
}
