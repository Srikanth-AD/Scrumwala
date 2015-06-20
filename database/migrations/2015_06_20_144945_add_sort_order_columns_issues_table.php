<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSortOrderColumnsIssuesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('issues', function (Blueprint $table) {
			$table->integer('sort_prev')->unsigned()->nullable();
			$table->integer('sort_next')->unsigned()->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('issues', function (Blueprint $table) {
			$table->dropColumn(['sort_prev', 'sort_next']);
		});
	}
}
