<?php namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model {

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'title',
		'description',
		'deadline',
		'project_id',
		'user_id',
		'type_id',
		'status_id',
		'sprint_id',
		'priority_order',
		'sort_prev',
		'sort_next',
	];

	protected $dates = ['deadline'];

	public function setDeadlineAttribute($date) {
		try {
			$this->attributes['deadline'] = Carbon::createFromFormat('Y-m-d', $date);
			$this->attributes['deadline']->hour = '23';
			$this->attributes['deadline']->minute = '55';
			$this->attributes['deadline']->second = '55';
		} catch (\Exception $e) {
			$this->attributes['deadline'] = NULL;
		}
	}

	public function getDeadlineAttribute($date) {
		if ($date != null) {
			return new Carbon($date);
		}
	}

	/**
	 * An issue belongs to a project
	 */
	public function project() {
		return $this->belongsTo('App\Project');
	}

	/**
	 * An issue belongs to a user
	 * @return type
	 */
	public function user() {
		return $this->belongsTo('App\User');
	}

	/**
	 * An issue has one issue type
	 */
	public function issueType() {
		return $this->hasOne('App\IssueType');
	}

	/**
	 * An issue has one sprint
	 */
	public function sprint() {
		return $this->hasOne('App\Sprint');
	}

	/**
	 * An issue status belongs to an issue
	 */
	public function issueStatus() {
		return $this->belongsTo('App\Issue');
	}

}
