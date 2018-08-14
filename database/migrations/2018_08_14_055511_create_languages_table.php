<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('languages')) {
            return;
        }
        Schema::create('languages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('iso_code', 2)->index();
            $table->string('title');
            $table->string('title_localized');
            $table->boolean('is_fallback')->default(0)->index();
            $table->boolean('is_visible')->default(1)->index();
            $table->softDeletes();
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('languages');
        Schema::enableForeignKeyConstraints();
    }
}
