<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laradium\Laradium\Models\Menu;

class AlterMenuItemsTableAddTypeRouteColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->enum('type', ['url', 'route', 'resource'])->default('url')->after('id');
            $table->string('route')->nullable()->after('resource');
        });

        $adminMenu = Menu::where('key', 'admin_menu')->first();
        if ($adminMenu) {
            foreach ($adminMenu->items as $item) {
                if ($item->resource) {
                    $item->update([
                        'type' => 'resource'
                    ]);
                }
            }
        }

        cache()->forget(Menu::$cacheKey);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('route');
        });
    }
}
