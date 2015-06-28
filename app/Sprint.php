<?php namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Sprint extends Model {

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'machine_name',
		'from_date',
		'to_date',
		'status_id',
		'project_id',
		'sort_order',
	];

	public $timestamps = true;

	protected $dates = ['from_date', 'to_date'];

	public function setFromDateAttribute($date) {
		if ($date) {
			$this->attributes['from_date'] = Carbon::createFromFormat('Y-m-d', $date);
			$this->attributes['from_date']->hour = '01';
			$this->attributes['from_date']->minute = '01';
			$this->attributes['from_date']->second = '01';
		} else {
			$this->attributes['from_date'] = null;
		}
	}

	/**
	 * [getFromDateAttribute return a carbon instance for a given date as string
	 * @param  string $date [description]
	 * @return object|boolean Carbon instance|false
	 */
	public function getFromDateAttribute($date) {
		if ($date) {
			return new Carbon($date);
		} else {
			return false;
		}
	}

	public function setToDateAttribute($date) {
		if ($date) {
			$this->attributes['to_date'] = Carbon::createFromFormat('Y-m-d', $date);
			$this->attributes['to_date']->hour = '23';
			$this->attributes['to_date']->minute = '55';
			$this->attributes['to_date']->second = '55';
		} else {
			$this->attributes['to_date'] = null;
		}
	}

	/**
	 * [getToDateAttribute return a carbon instance for a given date as string
	 * @param  string $date [description]
	 * @return object|boolean Carbon instance|false
	 */
	public function getToDateAttribute($date) {
		if ($date) {
			return new Carbon($date);
		} else {
			return false;
		}
	}

	/**
	 * A sprint has many issues
	 */
	public function issues() {
		return $this->hasMany('App\Issue');
	}

	/*
	 * A sprint belongs to a project
	 */
	public function project() {
		return $this->belongsTo('App\Project');
	}

	/**
	 * Returns if a sprint in a project is complete
	 * @return  boolean
	 */
	public function isComplete() {

		$issuesCount = $this->issues()
		                    ->select('status_id')
		                    ->where('sprint_id', '=', $this->id)
		                    ->where('project_id', '=', $this->project_id)->count();

		$issuesCompletedCount = $this->issues()
		                             ->select('status_id')
		                             ->where('sprint_id', '=', $this->id)
		                             ->where('project_id', '=', $this->project_id)
		                             ->whereIn('status_id', array(
			                             IssueStatus::getIdByMachineName('complete'),
			                             IssueStatus::getIdByMachineName('archive'),
		                             ))
		                             ->count();

		if ($issuesCompletedCount == $issuesCount) {
			return true;
		}
		return false;
	}

	/**
	 * getLatestIssueInSprint Get the latest issue from a sprint - by created at date
	 * @return latest issue in sprint
	 */
	public function getLatestIssueInSprint() {
		$archiveStatusId = IssueStatus::getIdByMachineName('archive');

		return $this->issues()
		            ->where('status_id', '!=', (int) $archiveStatusId)
		            ->orderBy('created_at', 'desc')
		            ->first();
	}

	/**
	 * getPreviousIssueBySortOrder get previous issue from sprint by sort order
	 * @param  int $issueId Issue Id
	 * @return
	 */
	public function getPreviousIssueBySortOrder($issueId) {
		return $this->issues()->where('sort_next', '=', $issueId)->first();
	}

	/**
	 * getNextIssueBySortOrder get next issue from sprint by sort order
	 * @param  int $issueId Issue Id
	 * @return
	 */
	public function getNextIssueBySortOrder($issueId) {
		return $this->issues()->where('sort_prev', '=', $issueId)->first();
	}

}
