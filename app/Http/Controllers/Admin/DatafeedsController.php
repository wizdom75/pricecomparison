<?php

namespace App\Http\Controllers\Admin;

use App\Brand;
use App\Match;
use App\Price;
use Exception;
use ZipArchive;
use App\Product;
use App\Category;
use App\Datafeed;
use App\Merchant;
use App\ProductImageLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\ProductCodes as ProductCode;
use Illuminate\Support\Facades\Storage;

class DatafeedsController extends Controller
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
        if($mId = Request()->get('mId')){
            $mId = Request()->get('mId');
            $datafeeds = Datafeed::where('merchant_id', $mId)->paginate(10);
            //dd($datafeeds);
        }else{
            $datafeeds = Datafeed::paginate(10);
        }

        return view('admin.datafeeds.index')->with(compact('datafeeds', 'mId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $mId = Request()->get('mId');
        $merchants = Merchant::orderBy('name', 'asc')->pluck('name', 'id');

        return view('admin.datafeeds.create')->with(compact('merchants', 'mId'));
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
            'merchantId' => 'required',
            'feed_url' => 'required'
        ]);

        $feed = new Datafeed;

        $feed->merchant_id = $request->input('merchantId');
        $feed->url = $request->input('feed_url');
        $feed->add_new_products = $request->input('add_new_products') ?? '1';
        $feed->match_by = $request->input('match_by') ?? 'mpn';
        $feed->save();

        return redirect()->back()->with('success', 'Datafeed successfully added.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('admin.datafeeds.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $feed = Datafeed::find($id);

        $mId = $feed->merchant_id;
        $merchants = Merchant::orderBy('name', 'asc')->pluck('name', 'id');

        return view('admin.datafeeds.edit')->with(compact('merchants', $merchants, 'feed', $feed));
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
            'merchantId' => 'required',
            'feed_url' => 'required'
        ]);

        $feed = Datafeed::find($id);
        $feed->merchant_id = $request->input('merchantId');
        $feed->url = $request->input('feed_url');

        $feed->save();

        return redirect()->back()->with('success', 'Datafeed successfully updates.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $feed = Datafeed::find($id);
        $feed->delete();
        return redirect()->back()->with('success', 'Feed deleted successfuly');
    }

    /**
     * Test run method to set the feed parameters
     */
    public function test($id)
    {
        $feed = Datafeed::find($id);
        $mId = $feed->merchant_id;
        $url = $feed->url;//url goes here

        $dest = 'storage/autofeeds/'.$mId;
        if(!is_dir($dest)){
            mkdir($dest, 0777, $url);
        }

        getRemoteFile($url, $dest.'/testrun');
        try {
            copy($url, $dest.'/testrun');
        } catch (Exception $e) {
            return 0;
        }

        $fname = 'testrun.csv';

        $zip = new ZipArchive;
        if ($zip->open($dest.'/testrun') === TRUE) {
            $zip->renameName($zip->getNameIndex(0), $fname);
            $zip->extractTo($dest, $fname);
            $zip->close();
            unlink($dest.'/testrun');
        }

        $handle = fopen($dest.'/'.$fname, 'r');
        $i = 0;
        while (($data = fgetcsv($handle, 0, ',')) !== FALSE){
            $params[] = $data;
            // count($data) is the number of columns
            $numcols = count($data);
            if ($i< 5) {
                continue;
            }

            $i++;
        }
        //print_r($params);
        //die;
        return view('admin.datafeeds.test')->with(compact('params', 'numcols', 'feed'));
    }

    /**
     * Rest post route that creates the settings form
     */
    public function testCreate(Request $request, $id)
    {
        $feed = Datafeed::find($id);
        $feed->column_isbn = null;
        foreach($request->params as $key => $value){
            if(isset($value)){
                switch ($value) {
                    case 'productName':
                        $productName = $key;
                        break;
                    case 'productDesc':
                        $productDesc = $key;
                        break;
                    case 'productPrice':
                        $productPrice = $key;
                        break;
                    case 'categoryName':
                        $categoryName = $key;
                        break;
                    case 'categoryId':
                        $categoryId = $key;
                        break;
                    case 'shipping':
                        $shipping = $key;
                        break;
                    case 'buyUrl':
                        $buyUrl = $key;
                        break;
                    case 'promoText':
                        $promoText = $key;
                        break;
                    case 'mpn':
                        $mpn = $key;
                        break;
                    case 'upc':
                        $upc = $key;
                        break;
                    case 'isbn':
                        $isbn = $key;
                        break;
                    case 'ean':
                        $ean = $key;
                        break;
                    case 'image':
                        $image = $key;
                        break;
                    case 'brand':
                        $brand = $key;
                        break;
                }
            }
        }


        $feed->column_name          = $productName ?? null;
        $feed->column_description   = $productDesc ?? null;
        $feed->column_price         = $productPrice ?? null;
        $feed->column_category_name = $categoryName ?? null;
        $feed->column_category_id   = $categoryId ?? null;
        $feed->column_shipping      = $shipping ?? null;
        $feed->column_buy_url       = $buyUrl ?? null;
        $feed->column_promo         = $promoText ?? null;
        $feed->column_mpn           = $mpn ?? null;
        $feed->column_upc           = $upc ?? null;
        $feed->column_isbn          = $isbn ?? null;
        $feed->column_ean           = $ean ?? null;
        $feed->column_image_url     = $image ?? null;
        $feed->column_brand         = $brand ?? null;
        $feed->add_new_products     = $request->add_new_products;
        $feed->match_by             = $request->match_by;

        $feed->save();

        return redirect('/admin/datafeeds')->with('success', 'Datafeed Parameters Added Successfully!');
    }

    /**
     * Run the datafeed to update prices
     */
    public function run($id)
    {
        //$feed = Datafeed::find($id);
        $feed = DB::table('datafeeds')->find($id);

        /**
         * Merchant ID is set here
         */
        $mId = $feed->merchant_id;

        /**
         * Datafeed url is set here
         */
        $url = $feed->url;
        $brandId = null;
        $category_id = null;
        $title = null;
        $slug = null;
        $mpn = null;
        $ean = null;
        $upc = null;
        $gtin = null;
        $isbn = null;
        $description = null;
        $min_price = null;
        $max_price = null;
        $brand_id = null;
        $update_price = null;
        $new_price = null;

        /**
         * Now we create the merchant autofeed
         * folder if it does not exist
         */
        $dest = 'storage/autofeeds/'.$mId;
        if(!is_dir($dest)){
            mkdir($dest, 0777, $url);
        }

        /**
         * Download the latest datafeed from merchant
         * and copy to our newly created folder / server
         */

        try {
            copy($url, $dest.'/feed');
        } catch (Exception $e) {
            return 0;
        }
        $fname = 'datafeed.csv';

        /**
         * Unzip / Extract datafeed to $fname and unlink the zip file
         */
        $zip = new ZipArchive;
        if ($zip->open($dest.'/feed') === TRUE) {
            $zip->renameName($zip->getNameIndex(0), $fname);
            $zip->extractTo($dest, $fname);
            $zip->close();
            unlink($dest.'/feed');
        }
        /**
         * Open the datafile with fopen and create a handle
         */
        $handle = fopen($dest.'/'.$fname, 'r');
        /**
         * Initiate counter that is used to skip hearders
         */
        $i = 0;

        /**
         * Now process the file
         */
        fgetcsv($handle, 0, ',');
        while (($data = fgetcsv($handle, 0, ',')) !== FALSE){
                /**
                 * Now we check if the brand column isset in feed parameters
                 * If isset we check if the value  is in csv file,
                 * If found we check DB for this value.
                 * If exists we skip record otherwise add the new brand
                 */
                if ($feed->column_brand !== null){
                    if($data[$feed->column_brand] !== null){
                        $brand = DB::table('brands')->where('name', $data[$feed->column_brand])->first();
                        if(!$brand){
                            DB::table('brands')->insert(
                                ['name' => $data[$feed->column_brand]]
                            );
                        }
                    }
                }
                    /**
                     * Here we check if this feed is allowed to add new products to our database
                     * If yes we add product as permitted otherwise we skip this step
                     */
                    if($feed->column_mpn){
                        $mpn = DB::table('product_codes')->where('mpn', $data[$feed->column_mpn])->first();
                    }
                    if($feed->column_ean){
                        $ean = DB::table('product_codes')->where('ean','=',$data[$feed->column_ean])->first();
                    }
                    if($feed->column_gtin){
                        $gtin = DB::table('product_codes')->where('gtin','=',$data[$feed->column_gtin])->first();
                    }
                    if($feed->column_upc){
                        $upc = DB::table('product_codes')->where('upc','=',$data[$feed->column_upc])->first();
                    }

                /**
                 * We check if we can add new products and that the
                 * product is not already in our database
                 */
                    if($feed->add_new_products && !$mpn || !$ean || !$gtin || !$upc){

                        //$product = new Product;
                        $nextId = Product::max('id')+1;
                        $product_id = Product::max('id');
                        if($feed->column_brand){
                            $brandId = DB::table('brands')->where('name', $data[$feed->column_brand])->first();
                        }

                        if($feed->column_category_id){
                            $category_id = $data[$feed->column_category_id];
                        }
                        if($feed->column_name){
                            $title = $data[$feed->column_name];
                            $slug = $nextId.'-'.makeSlug($data[$feed->column_name]);
                        }else{
                            echo $feed->column_name." - w\n";
                            continue;
                        }
                        if($feed->column_mpn && $feed->column_mpn){
                            $mpn = $data[$feed->column_mpn];
                        }
                        if($feed->column_ean && $feed->column_ean){
                            $ean = $data[$feed->column_ean];
                        }
                        if($feed->column_upc && $feed->column_upc){
                            $upc = $data[$feed->column_upc];
                        }
                        if($feed->column_gtin && $feed->column_gtin){
                            $gtin = $data[$feed->column_gtin];
                        }
                        if($feed->column_isbn && $feed->column_isbn){
                            $isbn = $data[$feed->column_isbn];
                        }
                        if($feed->column_description && $feed->column_description){
                            $description = $data[$feed->column_description];
                        }

                        $min_price = 0;
                        $max_price = 0;
                        if($brandId){
                            $brand_id = $brandId->id;
                        }



                       // $product->save();
                        $product_id = DB::table('products')->insertGetId([
                            'category_id' => $category_id,
                            'title' => $title,
                            'slug' => $slug,
                            'mpn' => $mpn,
                            'ean' => $ean,
                            'upc' => $upc,
                            'gtin' => $gtin,
                            'isbn' => $isbn,
                            'description' => $description,
                            'min_price' => $min_price,
                            'max_price' => $max_price,
                            'brand_id' => $brand_id,
                        ]);

                        /**
                         * We now check to see if there is a product image link
                         * if it exists we get this to download later
                         */

                        if($feed->column_image_url && $data[$feed->column_image_url]){
                           // $image = new ProductImageLink;
                            DB::table('product_image_links')->insert([
                                'product_id' => $product_id,
                                'merchant_id' => $mId,
                                'is_downloaded' => '0',
                                'download_path' => $data[$feed->column_image_url],
                            ]);

                           // $image->save();
                        }

                        /**
                         * This part will add product codes in in the product matching table
                        */
                        //$pc = new ProductCode;
                        DB::table('product_codes')->insert([
                            'product_id' => $product_id,
                            'mpn' => $mpn,
                            'ean' => $ean,
                            'upc' => $upc,
                            'gtin' => $gtin,
                            'isbn' => $isbn,
                            'title' => $title
                        ]);

                        //$pc->save();
                    }
                    /**
                     * This section will add product prices for merchant.
                     *
                     * We will search for product in the database based on the Match by parameter
                     * Once we locate the product we check if price is already in database
                     */
                    if($feed->match_by === 'mpn'){
                        $match = DB::table('product_codes')->where('mpn','=',$data[$feed->column_mpn])->first();
                    }elseif($feed->match_by === 'ean'){
                        $match = DB::table('product_codes')->where('ean','=',$data[$feed->column_ean])->first();
                    }elseif($feed->match_by === 'isbn'){
                        $match = DB::table('product_codes')->where('isbn','=',$data[$feed->column_isbn])->first();
                    }elseif($feed->match_by === 'gtin'){
                        $match = DB::table('product_codes')->where('gtin','=',$data[$feed->column_gtin])->first();
                    }elseif($feed->match_by === 'upc'){
                        $match = DB::table('product_codes')->where('upc','=',$data[$feed->column_upc])->first();
                    }elseif($feed->match_by === 'name'){
                        $match = DB::table('product_codes')->where('title','=',$data[$feed->column_name])->first();
                    }

                    /**
                     * Is price in database?
                     * Lets find out
                     */
                    if($match){
                        $prod_id = $match->product_id;
                        $fields = ['product_id' => $prod_id, 'merchant_id' => $mId];
                        $price = DB::table('prices')->where($fields)->first();

                        if(!$price){
                            //This bit will add a new price if not in database yet
                            // $price = new Price;
                            // $price->product_id = $prod_id;
                            // $price->merchant_id = $mId;
                            // $price->amount = (float)$data[$feed->column_price];
                            // $price->shipping = (float)(!$data[$feed->column_shipping])??0.00;
                            // $price->product_title = $data[$feed->column_name];
                            // $price->buy_link = $data[$feed->column_buy_url];
                            // $price->save();

                            $shipping = 0;
                            if (isset($feed->column_shipping) && is_int($feed->column_shipping)) {
                                $shipping = $data[$feed->column_shipping] ?? 0;
                            }

                            $new_price[] = [

                                'product_id' => $prod_id,
                                'merchant_id' => $mId,
                                'amount' => (float)$data[$feed->column_price],
                                'shipping' => $shipping,
                                'product_title' => $data[$feed->column_name],
                                'buy_link' => $data[$feed->column_buy_url]
                            ];

                            // if($feed->column_image_url && $data[$feed->column_image_url]){
                            //     $image = new ProductImageLink;
                            //     $image->product_id = $match->product_id;
                            //     $image->merchant_id = $mId;
                            //     $image->download_path = $data[$feed->column_image_url];
                            //     $image->save();
                            // }

                        }else{
                            $shipping = 0;
                            if (isset($feed->column_shipping) && is_int($feed->column_shipping)) {
                                $shipping = $data[$feed->column_shipping] ?? 0;
                            }
                             $update_price[] = [
                                'id'=> $price->id,
                                'product_id' => $prod_id,
                                'merchant_id' => $mId,
                                'amount' => (float)$data[$feed->column_price],
                                'shipping' => (float) $shipping,
                                'product_title' => $data[$feed->column_name],
                                'buy_link' => $data[$feed->column_buy_url]
                            ];
                        }

                    }else{
                        echo 'Product not found.<br/>';
                    }
                }


        if($new_price){
            DB::table('prices')->insert($new_price);
        }
        // if($update_price){
        //     DB::table('prices')->update($update_price);
        // }

        fclose($handle);

         return redirect()->back()->with('success', 'Product feed ran successfully');
    }
}
