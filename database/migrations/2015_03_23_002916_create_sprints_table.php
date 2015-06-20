<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSprintsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('sprints', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name')->index();
			$table->string('machine_name')->index();
			$table->timestamp('from_date')->nullable();
			$table->timestamp('to_date')->nullable();
			$table->timestamps();
			$table->integer('project_id')->unsigned();
			$table->integer('sort_order')->unsigned()->index();
			$table->integer('status_id')->unsigned();
			$table->foreign('status_id')
			->references('id')
			->on('sprint_statuses');
			$table->foreign('project_id')
			->references('id')
			->on('projects');
			$table->unique(['machine_name', 'project_id']);
			$table->unique(['sort_order', 'project_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('sprints');
	}

}
