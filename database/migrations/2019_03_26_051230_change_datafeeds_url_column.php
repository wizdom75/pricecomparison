<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDatafeedsUrlColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('datafeeds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('merchant_id');
            $table->text('url');
            $table->tinyInteger('column_title')->unsigned()->nullable();
            $table->tinyInteger('column_price')->unsigned()->nullable();
            $table->tinyInteger('column_shipping')->unsigned()->nullable();
            $table->tinyInteger('column_url')->unsigned()->nullable();
            $table->tinyInteger('column_promo')->unsigned()->nullable();
            $table->tinyInteger('column_mpn')->unsigned()->nullable();
            $table->tinyInteger('column_upc')->unsigned()->nullable();
            $table->tinyInteger('column_isbn')->unsigned()->nullable();
            $table->tinyInteger('column_ean')->unsigned()->nullable();
            $table->tinyInteger('column_gtin')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('datafeeds');
    }
}