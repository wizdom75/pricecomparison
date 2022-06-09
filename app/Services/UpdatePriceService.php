<?php

namespace App\Services;

use App\Brand;
use App\Price;
use Exception;
use ZipArchive;
use App\Product;
use App\Datafeed;
use App\ProductCodes;
use App\ProductImageLink;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UpdatePriceService
{
    use AddProductServiceTrait;

    protected function createDirectory(string $merchant_id) : ?string
    {
        $folder = 'storage/autofeeds/'.$merchant_id;
        if(!is_dir($folder)){
            try {
                mkdir($folder, 0777, true);
            } catch (Exception $e) {
                echo $e->getMessage();
                return null;
            }
        }
        return $folder;
    }

    protected function unzip($folder, $filename)
    {
        $zip = new ZipArchive;
        try {
            if ($zip->open($folder.'/zipped.zip')) {
                $zip->renameName($zip->getNameIndex(0), $filename);
                $zip->extractTo($folder, $filename);
                $zip->close();
                unlink($folder.'/zipped.zip');
            }
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
            return false;
        }

    }

    protected function productExists($data, $match_by, $column)
    {
      return ProductCodes::where($match_by, $data[$column])->first();
    }

    protected function getPrice($merchant_id, $product_id)
    {
      return Price::where(['merchant_id' => $merchant_id, 'product_id' => $product_id])->first();
    }

    protected function getBrandIdByName(string $name)
    {
        if (!$name) {
            return (object) ['id' => 0, 'name' => 'N/A'];
        }

        if (!$brand = Brand::where('name', $name)->first()) {
            $brand = Brand::create(['name' => $name]);
        }
        return $brand;
    }

    protected function addNewPrice($product_id, $merchant_id, $amount, $shipping, $product_title, $promo_text, $buy_link)
    {
        return Price::create([
            'product_id'    => $product_id,
            'merchant_id'   => $merchant_id,
            'amount'        => $amount,
            'shipping'      => $shipping,
            'product_title' => $product_title,
            'promo_text'    => $promo_text,
            'buy_link'      => $buy_link,
        ]);
    }

    protected function addImageLinks(array $images_data)
    {
        return ProductImageLink::create($images_data);
    }

    public function run(array $ids)
    {
        if (count($ids)) {
            $datafeeds = Datafeed::whereIn('merchant_id', $ids)->get();
        } else {
            $datafeeds = Datafeed::all();
        }

        $insert_price_id = (int) DB::table('products')->latest('id')->first()->id;

        foreach ($datafeeds as $datafeed) {

            $url                    = $datafeed->url;
            $merchant_id            = $datafeed->merchant_id;
            $column_name            = $datafeed->column_name;
            $column_description     = $datafeed->column_description;
            $column_price           = $datafeed->column_price;
            $column_category_name   = $datafeed->column_category_name;
            $column_category_id     = $datafeed->column_category_id;
            $column_shipping        = $datafeed->column_shipping;
            $column_buy_url         = $datafeed->column_buy_url;
            $column_promo           = $datafeed->column_promo;
            $column_image_url       = $datafeed->column_image_url;
            $column_mpn             = $datafeed->column_mpn;
            $column_upc             = $datafeed->column_upc;
            $column_isbn            = $datafeed->column_isbn;
            $column_ean             = $datafeed->column_ean;
            $column_gtin            = $datafeed->column_gtin;
            $column_brand           = $datafeed->column_brand;
            $add_new_products       = $datafeed->add_new_products;
            $match_by               = $datafeed->match_by;

            if (!$folder = $this->createDirectory($merchant_id)) {
                return  "Failed to create directory for merchant datafeed\n";
            }

            if (!copy($url, $folder.'/zipped.zip')) {
                return "Copying datafeed file from remote host\n";
            }

            if (!$this->unzip($folder, '/autofeed.csv')) {
                return "Could not unzip file\n";
            }

             $handle = fopen($folder.'/autofeed.csv', 'r');
             fgetcsv($handle, 0, ',');
             while (($data = fgetcsv($handle, 0, ',')) !== false) {

                if ($add_new_products && !$this->productExists($data, $match_by, ${'column_'.$match_by})) {
                    $new_product_id = ++$insert_price_id;
                    $newProductData = [
                        'id'            => $new_product_id,
                        'category_id'   => $data[$column_category_id],
                        'title'         => $data[$column_name],
                        'slug'          => $new_product_id.'-'.Str::snake(strtolower($data[$column_name])),
                        'mpn'           => $data[$column_mpn] ?? NULL,
                        'ean'           => $data[$column_ean] ?? NULL,
                        'upc'           => $data[$column_upc] ?? NULL,
                        'gtin'          => $data[$column_gtin] ?? NULL,
                        'isbn'          => $data[$column_isbn] ?? NULL,
                        'description'   => $data[$column_description] ?? '',
                        'brand_id'      => $this->getBrandIdByName($data[$column_brand])->id,
                        'min_price'     => 0,
                        'max_price'     => 0,
                    ];

                    $newProductCodesData = [
                        'product_id'    => $new_product_id,
                        'title'         => $data[$column_name],
                        'mpn'           => $data[$column_mpn] ?? NULL,
                        'ean'           => $data[$column_ean] ?? NULL,
                        'upc'           => $data[$column_upc] ?? NULL,
                        'gtin'          => $data[$column_gtin] ?? NULL,
                        'isbn'          => $data[$column_isbn] ?? NULL,
                    ];

                    $newImageLinksData = [
                        'product_id'    => $new_product_id,
                        'merchant_id'   => $merchant_id,
                        'download_path' => $data[$column_image_url],
                        'is_downloaded' => '0',
                    ];
                    $this->createProduct($newProductData, $newProductCodesData);
                    $this->addImageLinks($newImageLinksData);
                    echo "Add new product ->  $data[$column_name] \n";
                }

                if ($product_code = $this->productExists($data, $match_by, ${'column_'.$match_by})) {
                    echo "Price updating for product ID -> $product_code->product_id \n";

                    if ($oldPrice = $this->getPrice($merchant_id, $product_code->product_id)) {
                        $oldPrice->delete();
                        echo "$product_code->product_id old price deleted\n";
                    }
                    $this->addNewPrice(
                        $product_code->product_id,
                        $merchant_id,
                        $data[$column_price],
                        $data[$column_shipping] ?? '',
                        $data[$column_name],
                        $data[$column_promo] ?? '',
                        $data[$column_buy_url]
                    );
                    echo "$product_code->product_id new price (£$data[$column_price]) added\n";

                }
             }
        }
    }

    protected function setMinAndMaxPrice($id)
    {
        $product = Product::find($id);

        $min_price = Price::where('product_id', $id)->min('amount');
        $max_price = Price::where('product_id', $id)->max('amount');

        if($min_price){
            $product->min_price = $min_price;
            $product->max_price = $max_price;
            $product->save();

            return redirect()->back()->with('success', 'Product prices set');
        }else{
            $product->min_price = 0.00;
            $product->max_price = 0.00;
            $product->save();

            return redirect()->back()->with('success', 'Product prices set');
        }

    }

    public function setMinAndMaxPriceForAllProducts()
    {
        ini_set('max_execution_time', 0); //No limit
        $products = Product::all();
        foreach($products as $product){
            $this->setMinAndMaxPrice($product->id);
        }
    }
}
