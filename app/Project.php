<?php namespace App;

use App\IssueStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Project extends Model {

	public static $projectTypes = ['scrum'=>'scrum', 'kanban'=>'kanban'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'type',
		'slug',
		'issue_prefix',
		'deadline',
		'user_id', // need this for Faker
	];


	protected $dates = ['deadline'];

	public function setDeadlineAttribute($date) {
		if ($date) {
			$this->attributes['deadline'] = Carbon::createFromFormat('Y-m-d', $date);
			$this->attributes['deadline']->hour = '23';
			$this->attributes['deadline']->minute = '55';
			$this->attributes['deadline']->second = '55';
		} else {
			$this->attributes['deadline'] = null;
		}
	}

	public function getDeadlineAttribute($date) {
		if ($date) {
			return new Carbon($date);
		} else {
			return false;
		}
	}

	public function user() {
		return $this->belongsTo('App\User');
	}

	/*
	 * A project can have many issues
	 */
	public function issues() {
		return $this->hasMany('App\Issue');
	}

	/**
	 * Get issues that are active (not archived)
	 * @return  collection
	 */

	public function getActiveIssues() {
		$archiveStatusId = IssueStatus::getIdByMachineName('archive');
		return $this->issues()->where('status_id', '!=', (int) $archiveStatusId)->get();
	}

	public function getNumberOfActiveIssues() {
		$archiveStatusId = IssueStatus::getIdByMachineName('archive');
		return $this->issues()->where('status_id', '!=', (int) $archiveStatusId)->count();
	}

	/*
	 * A project can have many sprints
	 */
	public function sprints() {
		return $this->hasMany('App\Sprint');
	}

	/**
	 * Get the list of sprints from a project that are not complete
	 * @return collection $sprints
	 */
	public function getSprints() {
		$sprints = $this->sprints()
		                ->where('status_id', '!=', SprintStatus::getIdByMachineName('complete'))
		                ->orderBy('sort_order', 'desc')->get();
		return $sprints;
	}

	/**
	 * Get the collection of issues corresponding to a given sprint - that are not archived
	 * @param integer $sprintId
	 * @return collection
	 */
	public function getIssuesFromSprint($sprintId) {
		$archiveStatusId = IssueStatus::getIdByMachineName('archive');
		return $this->issues()
		            ->where('status_id', '!=', (int) $archiveStatusId)
		            ->where('sprint_id', '=', (int) $sprintId)
                        ->orderBy('priority_order')
                        ->orderBy('id')
                        ->get();
	}

	/**
	 * createBacklogSprint when a new project is created,
	 * create a sprint named backlog by default and set its status to inactive
	 * @param  int $projectId
	 */
	public function createBacklogSprint($projectId) {
		if ($projectId) {
			$sprint = new Sprint;
			$sprint->name = 'Backlog';
			$sprint->machine_name = 'backlog';
			$sprint->status_id = SprintStatus::getIdByMachineName('inactive');
			$sprint->project_id = (int) $projectId;
			$sprint->sort_order = 0;
			$sprint->save();
		}
	}

	/**
	 * Get the active sprint for a given project
	 * @return first sprint from collection
	 */
	public function getActiveSprint() {
		return $this->sprints()
		            ->where('status_id', '=', SprintStatus::getIdByMachineName('active'))
		            ->get()->first();
	}

	/**
	 * getBacklogSprint Get the backlog sprint for a project
	 * @return backlog sprint from collection
	 */
	public function getBacklogSprint() {
		return $this->sprints()->where('machine_name', '=', 'backlog')->get()->first();

	}
}