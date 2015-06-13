<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class IssueType extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['label'];

    public $timestamps = false;
    
    /**
     * An IssueType can be used by many issues
     */
    public function issues()
    {
        return $this->belongsToMany('App\Issue');
    }

}
