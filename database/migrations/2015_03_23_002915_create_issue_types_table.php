<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateIssueTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('issue_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('machine_name')->unique();
			$table->string('label')->unique();
		});
		DB::insert("insert into issue_types (machine_name,label) values
					('bug', 'Bug'),
					('task', 'Task'),
					('story', 'Story'),
					('improvement','Improvement')");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('issue_types');
	}

}
