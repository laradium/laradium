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

        $menus = [];
        $laradium = app(\Laradium\Laradium\Base\Laradium::class);

        foreach ($laradium->resources() as $resource) {
            $laradium->register($resource);
        }

        if ($laradium->all()) {
            foreach ($laradium->all() as $resource) {
                if (in_array($resource, config('laradium.disable_menus', []))) {
                    continue;
                }

                $resource = new $resource;
                $menus['Admin menu'][] = [
                    'is_active'    => 1,
                    'resource'     => get_class($resource),
                    'translations' => [
                        'name' => $resource->getBaseResource()->getName(),
                        'url'  => ''
                    ],
                    'icon'         => $this->getIcon($resource)
                ];
            }
        }

        menu()->seed($menus);

        if ($menus = config('laradium.menus', [])) {
            menu()->seed($menus);
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

    /**
     * @param $resource
     * @return mixed|string
     */
    protected function getIcon($resource)
    {
        $icons = [
            'LanguageResource'    => 'fa fa-language',
            'TranslationResource' => 'fa fa-globe',
            'PageResource'        => 'fa fa-file-text-o',
            'MenuResource'        => 'fa fa-link',
            'SettingResource'     => 'fa fa-cogs'
        ];

        $resource = array_last(explode('\\', get_class($resource)));

        return $icons[$resource] ?? null;
    }
}
