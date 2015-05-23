<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Sprint extends Model {

    protected $fillable = [
        'name',
        'machine_name',
        'from_date',
        'to_date',
        'status_id',
        'project_id',
        'sort_order'
    ];

    public $timestamps = true;

    protected $dates = ['from_date', 'to_date'];

    public function setFromDateAttribute($date)
    {
        if($date)
        {
            $this->attributes['from_date'] = Carbon::createFromFormat('Y-m-d', $date);
            $this->attributes['from_date']->hour = '01';
            $this->attributes['from_date']->minute = '01';
            $this->attributes['from_date']->second = '01';
        } else {
            $this->attributes['from_date'] = null;
        }
    }

    public function getFromDateAttribute($date)
    {
        if($date)
        {
            return new Carbon($date);
        } else {
            return false;
        }
    }

    public function setToDateAttribute($date)
    {
        if($date)
        {
            $this->attributes['to_date'] = Carbon::createFromFormat('Y-m-d', $date);
            $this->attributes['to_date']->hour = '23';
            $this->attributes['to_date']->minute = '55';
            $this->attributes['to_date']->second = '55';
        } else {
            $this->attributes['to_date'] = null;
        }
    }

    public function getToDateAttribute($date)
    {
        if($date)
        {
            return new Carbon($date);
        } else {
            return false;
        }
    }

    /**
     * A sprint has many issues
     * @return type
     */
    public function issues()
    {
        return $this->hasMany('App\Issue');
    }


    /*
     * A sprint belongs to a project
     */
    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    /**
     * Returns if a sprint in a project is complete
     */
    public function isComplete()
    {

        $issuesCount = $this->issues()
        ->select('status_id')
        ->where('sprint_id','=',$this->id)
        ->where('project_id', '=',$this->project_id)->count();

        $issuesCompletedCount = $this->issues()
        ->select('status_id')
        ->where('sprint_id','=',$this->id)
        ->where('project_id', '=',$this->project_id)
        ->whereIn('status_id', array(
            IssueStatus::getIdByMachineName('complete'), 
            IssueStatus::getIdByMachineName('archive')
            ))
        ->count();

        if($issuesCompletedCount == $issuesCount)
        {
            return true;
        }
        return false;
    }

}
