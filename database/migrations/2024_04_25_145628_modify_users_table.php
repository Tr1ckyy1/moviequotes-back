<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up()
	{
		Schema::table('users', function (Blueprint $table) {
			$table->renameColumn('name', 'username');
			$table->unique('username');
			$table->string('password')->nullable()->change();
			$table->string('profile_image')->nullable();
			$table->string('google_id')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down()
	{
		Schema::table('users', function (Blueprint $table) {
			$table->renameColumn('username', 'name');
		});
	}
};
