<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuItemTranslationsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_item_translations', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->nullable();
            $table->string('url')->nullable();

            $table->string('locale')->nullable()->index();
            $table->unsignedInteger('menu_item_id')->nullable();
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');

            $table->timestamps();
        });

        if ($menus = config('aven.menus', [])) {
            foreach ($menus as $name => $menuItems) {
                $m = \Netcore\Aven\Models\Menu::create([
                    'key' => str_slug($name, '_')
                ]);

                foreach (translate()->languages() as $language) {
                    $m->translations()->firstOrCreate([
                        'locale' => $language['iso_code'],
                        'name'   => $name
                    ]);
                }

                foreach ($menuItems as $item) {
                    $menuItem = $m->items()->create(array_except($item, 'translations'));

                    foreach (translate()->languages() as $language) {
                        $translations = $item['translations'];
                        $translations['locale'] = $language['iso_code'];

                        $menuItem->translations()->firstOrCreate($translations);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_item_translations');
    }
}
