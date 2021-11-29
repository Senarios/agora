<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeConstantBrands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->string('brand_category_tag1')->nullable(true)->change();
            $table->string('brand_category_tag2')->nullable(true)->change();
            $table->string('brand_category_tag3')->nullable(true)->change();
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->string('brand_category_tag1')->nullable(false)->change();
            $table->string('brand_category_tag2')->nullable(false)->change();
            $table->string('brand_category_tag3')->nullable(false)->change();
        });
    }
}