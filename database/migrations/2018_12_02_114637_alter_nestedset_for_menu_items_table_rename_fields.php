<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlterNestedsetForMenuItemsTableRenameFields extends Migration
{
    /**
     * AlterNestedsetForMenuItemsTableRenameFields constructor.
     */
    public function __construct()
    {
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->renameColumn('lft', 'left');
            $table->renameColumn('rgt', 'right');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->renameColumn('left', 'lft');
            $table->renameColumn('right', 'rgt');
        });
    }
}
