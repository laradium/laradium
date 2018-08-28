<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(0)->after('id');
        });

        // Seed default admin
        $userClass = config('auth.providers.users.model');
        $user = new $userClass;
        $user->name = 'System Admin';
        $user->email = config('aven.user.email');
        $user->password = bcrypt(config('aven.user.password'));
        $user->is_admin = 1;
        $user->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
}
