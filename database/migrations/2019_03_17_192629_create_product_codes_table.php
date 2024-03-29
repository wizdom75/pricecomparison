<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('product_codes')) {
            Schema::create('product_codes', function (Blueprint $table) {
                $table->increments('id');
                $table->bigInteger('product_id');
                $table->string('title')->nullable();
                $table->string('mpn')->nullable();
                $table->string('gtin')->nullable();
                $table->string('ean')->nullable();
                $table->string('isbn')->nullable();
                $table->string('upc')->nullable();
                $table->timestamps();
            });
            DB::statement('ALTER TABLE `product_codes` ADD FULLTEXT full(`title`,`mpn`,`ean`,`upc`,`gtin`,`isbn`)');
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_codes');
    }
}
