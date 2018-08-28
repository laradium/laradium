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
        if (!Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_admin')->after('email')->default(0)->index();
            });
        }
        if (Schema::hasColumn('users', 'name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('name', 'first_name');
            });
            Schema::table('users', function (Blueprint $table) {
                $table->string('last_name')->nullable()->after('first_name');
            });
        }

        // Seed default admin
        $userClass = config('auth.providers.users.model');
        $user = new $userClass;
        $user->first_name = 'System';
        $user->last_name = 'Admin';
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
        if (Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_admin');
            });
        }

        if (Schema::hasColumn('users', 'first_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('first_name', 'name');
                $table->dropColumn('last_name');
            });
        }

        if (config('netcore.module-user.socialite')) {
            Schema::dropIfExists('user_oauth_identities');
        }
    }
}
