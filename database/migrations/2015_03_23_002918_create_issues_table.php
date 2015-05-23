<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('issues', function(Blueprint $table)
		{
			$table->increments('id');                        
			$table->string('title');                        
            $table->text('description');
			$table->timestamp('deadline')->nullable();
			$table->timestamps();
            $table->integer('project_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('type_id')->unsigned();
            $table->integer('sprint_id')->unsigned();
            $table->integer('status_id')->unsigned();
            $table->integer('priority_order')->unsigned();
            $table->foreign('project_id')
                    ->references('id')
                    ->on('projects');

            $table->foreign('user_id')
                    ->references('id')
                    ->on('users');

            $table->foreign('type_id')
                ->references('id')
                ->on('issue_types');

            $table->foreign('sprint_id')
                ->references('id')
                ->on('sprints');

            $table->foreign('status_id')
                ->references('id')
                ->on('issue_statuses');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('issues');
	}

}
