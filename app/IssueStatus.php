<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class IssueStatus extends Model {

    protected $fillable = [
        'label',
        'sort_order'
    ];

    public $timestamps = false;

    /**
     * IssueStatus can be used by many issues
     * @return type
     */
    public function issues()
    {
        return $this->belongsToMany('App\Issue');
    }

    /**
     * Get Id of a issue status by its machine name
     * @param string $machineName
     * @return bool|int
     */
    public static function getIdByMachineName($machineName)
    {
        $id = IssueStatus::where('machine_name', '=', $machineName)->get()->first()->id;
        if($id)
        {
            return (int) $id;
        }
        else {
            return false;
        }
    }

    /**
     * Get issue statuses by sort_order
     */
    public static function getBySortOrder()
    {
        return IssueStatus::where('machine_name', '!=', 'archive')
                ->orderBy('sort_order', 'asc')->get();
    }

}
