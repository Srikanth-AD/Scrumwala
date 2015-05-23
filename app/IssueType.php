<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class IssueType extends Model {

	protected $fillable = [
		'label'
    	];
        
        public $timestamps = false;
        
        /**
         * IssueType can be used by many issues
         * @return type
         */
        public function issues()
        {
            return $this->belongsToMany('App\Issue');
        }

}
