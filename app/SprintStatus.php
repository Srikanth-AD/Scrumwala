<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class SprintStatus extends Model {

    protected $fillable = [
    'label',
    'sort_order'
    ];

    public $timestamps = false;

    /**
     * A SprintStatus can be used by many sprints
     * @return type
     */
    public function sprints()
    {
        return $this->belongsToMany('App\Sprint');
    }

    /**
     * Get id of a sprint status by it's machine name
     * @param string $machineName
     * @return bool|int
     */
    public static function getIdByMachineName($machineName)
    {
        $id = SprintStatus::where('machine_name', '=', $machineName)->get()->first()->id;
        if($id)
        {
            return (int) $id;
        }
        else {
            return false;
        }
    }

    /**
     * Get sprint statuses by sort_order in ascending order
     */
    public static function getBySortOrder()
    {
        return SprintStatus::where('machine_name', '!=', 'archive')
        ->orderBy('sort_order', 'asc')->get();
    }

}
