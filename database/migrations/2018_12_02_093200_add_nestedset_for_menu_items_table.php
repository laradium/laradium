<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNestedsetForMenuItemsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->integer('parent_id')->nullable()->after('resource');
            $table->integer('left')->nullable()->after('parent_id');
            $table->integer('right')->nullable()->after('left');
            $table->integer('depth')->nullable()->after('right');
        });

        if (!\Laradium\Laradium\Models\MenuItem::get()->count()) {
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
                    if ($resource->isShared()) {
                        continue;
                    }

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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('parent_id', 'left', 'right', 'depth');
        });
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
            'SettingResource'     => 'fa fa-cogs',
            'UserResource'        => 'fa fa-users',
            'SystemLogResource'   => 'fa fa-tachometer',
            'DocumentResource'    => 'fa fa-file-text'
        ];

        $resource = array_last(explode('\\', get_class($resource)));

        return $icons[$resource] ?? null;
    }
}
