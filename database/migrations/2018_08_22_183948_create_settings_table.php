<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');

            $table->string('group')->nullable()->index();
            $table->string('name')->nullable();
            $table->string('key')->unique()->index();
            $table->string('type')->default('text');
            $table->text('meta')->nullable();
            $table->boolean('has_manager')->default(0);
            $table->boolean('is_translatable')->default(0);

            $table->string('file_file_name')->nullable();
            $table->integer('file_file_size')->nullable();
            $table->string('file_content_type')->nullable();
            $table->timestamp('file_updated_at')->nullable();

            $table->mediumText('non_translatable_value')->nullable();


            $table->timestamps();
        });

        Schema::create('setting_translations', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('setting_id');
            $table->foreign('setting_id')->references('id')->on('settings')->onDelete('cascade');
            $table->string('locale')->index();

            $table->mediumText('value')->nullable();
        });

        $menus = [
            'Admin menu' => [
                [
                    'is_active'    => 1,
                    'translations' => [
                        'name' => 'Settings',
                        'url'  => '/admin/settings',
                    ]
                ],
            ]
        ];

        menu()->seed($menus);

        $settings = config('laradium-setting.default_settings');
        setting()->seed($settings);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting_translations');
        Schema::dropIfExists('settings');
    }
}
