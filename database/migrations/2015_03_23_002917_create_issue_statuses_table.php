<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssueStatusesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('issue_statuses', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('machine_name')->unique();
			$table->string('label')->unique();
			$table->integer('sort_order')->unsigned()->unique();
		});

		DB::insert("insert into issue_statuses (machine_name,label,sort_order) values
					('archive','Archive',0),
					('todo', 'To Do', 1),
					('inprogress', 'In Progress',2),
					('complete', 'Complete',3)");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('issue_statuses');
	}

}
