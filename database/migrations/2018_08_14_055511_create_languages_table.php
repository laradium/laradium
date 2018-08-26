<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Http\UploadedFile;

class CreateLanguagesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('iso_code', 2)->index();
            $table->string('title');
            $table->string('title_localized');
            $table->boolean('is_fallback')->default(0)->index();
            $table->boolean('is_visible')->default(1)->index();

            $table->string('icon_file_name')->nullable();
            $table->integer('icon_file_size')->nullable();
            $table->string('icon_content_type')->nullable();
            $table->timestamp('icon_updated_at')->nullable();

            $table->timestamps();
        });

        if ($languages = config('aven.languages', [])) {
            foreach ($languages as $language) {
                $lang = \Netcore\Aven\Models\Language::create(array_except($language, 'icon'));
                if (isset($language['icon'])) {
                    $image = new \Symfony\Component\HttpFoundation\File\File($language['icon']);
                    $file = new UploadedFile($image, $image->getBasename(), $image->getMimeType(), null, null, true);
                    $lang->icon = $file;
                    $lang->save();
                }
            }

            cache()->forget('languages');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('languages');
    }
}
