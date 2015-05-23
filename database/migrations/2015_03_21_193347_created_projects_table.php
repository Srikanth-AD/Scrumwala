<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatedProjectsTable extends Migration {

	/**
	 * Create projects table
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('projects', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->string('name')->unique();
			$table->string('slug')->unique();
			$table->string('issue_prefix')->unique();
			$table->timestamp('deadline')->nullable();
			$table->timestamps();
			$table->foreign('user_id')
				->references('id')
				->on('users')
				->onDelete('cascade');
		});
	}

	/**
	 * Drop projects table
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('projects');
	}

}
