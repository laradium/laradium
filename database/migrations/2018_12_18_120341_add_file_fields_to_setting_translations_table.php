<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileFieldsToSettingTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting_translations', function (Blueprint $table) {
            $table->string('file_file_name')->nullable()->after('setting_id');
            $table->integer('file_file_size')->nullable()->after('file_file_name');
            $table->string('file_content_type')->nullable()->after('file_file_size');
            $table->timestamp('file_updated_at')->nullable()->after('file_content_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setting_translations', function (Blueprint $table) {
            $table->dropColumn([
                'file_file_name',
                'file_file_size',
                'file_content_type',
                'file_updated_at'
            ]);
        });
    }
}
