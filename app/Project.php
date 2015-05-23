<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\IssueStatus;
use Carbon\Carbon;
use DB;
class Project extends Model {

    protected $fillable = [
        'name',
        'slug',
        'issue_prefix',
        'deadline',
        'user_id' // need this for Faker
    ];

    protected $dates = ['deadline'];

    public function setDeadlineAttribute($date)
    {
        if($date)
        {
            $this->attributes['deadline'] = Carbon::createFromFormat('Y-m-d', $date);
            $this->attributes['deadline']->hour = '23';
            $this->attributes['deadline']->minute = '55';
            $this->attributes['deadline']->second = '55';
        } else {
            $this->attributes['deadline'] = null;
        }
    }

    public function getDeadlineAttribute($date)
    {
        if($date)
        {
            return new Carbon($date);
        } else {
            return false;
        }
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /*
     * A project can have many issues
     */
    public function issues()
    {
        return $this->hasMany('App\Issue');
    }

    public function getActiveIssues()
    {
        $archiveStatusId = IssueStatus::getIdByMachineName('archive');
        return $this->issues()->where('status_id', '!=', (int) $archiveStatusId)->get();
    }

    /*
     * A project can have many sprints
     */
    public function sprints()
    {
        return $this->hasMany('App\Sprint');
    }

    /**
     * Get the list of sprints from a project
     * @return mixed
     */
    public function getSprints()
    {
        $sprints = $this->sprints()
                    ->where('status_id', '!=', SprintStatus::getIdByMachineName('complete'))
                    ->orderBy('sort_order','desc')->get();
        return $sprints;
    }

    /**
     * Get the issues corresponding to a given sprint
     * @param $sprintId
     * @return mixed
     */
    public function getIssuesFromSprint($sprintId)
    {
        $archiveStatusId = IssueStatus::getIdByMachineName('archive');
        return $this->issues()
            ->where('status_id', '!=', (int) $archiveStatusId)
            ->where('sprint_id', '=', (int) $sprintId)->get();
    }

    /** When a new project is created, create a sprint named backlog by default */
    public function createBacklogSprint($projectId)
    {
        if($projectId)
        {
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
     * @return mixed
     */
    public function getActiveSprint()
    {
        return $this->sprints()->where('status_id', '=', SprintStatus::getIdByMachineName('active'))->get()->first();
    }

    public function getBacklogSprint()
    {
        return $this->sprints()->where('machine_name', '=', 'backlog')->get()->first();

    }
}