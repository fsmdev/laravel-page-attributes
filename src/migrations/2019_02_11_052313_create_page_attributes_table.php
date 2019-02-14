<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('context')->unsigned()->nullable();
            $table->char('language', 2)->nullable();
            $table->char('name', 30)->nullable();
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['context', 'language', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_attributes');
    }
}
