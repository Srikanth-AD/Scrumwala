<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SprintRequest;
use Illuminate\Support\Facades\Redirect;
use App\Sprint;
use App\Project;
use App\SprintStatus;
use DB;
use Session;
use Log;

class SprintsController extends Controller {

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Add a sprint from the Project - Plan page
     */
    public function add(SprintRequest $request)
    {
        if($request->name && $request->project_id)
        {
            Sprint::create([
                'name' => $request->name,
                'machine_name' => strtolower(preg_replace('/\s+/', '', $request->name)),
                'status_id' => SprintStatus::getIdByMachineName('inactive'),
                'project_id' => (int) $request->project_id,
                'sort_order' => (int) DB::table('sprints')->where('project_id', '=', $request->project_id)->max('sort_order') + 1
                ]);
        }

        Session::flash('sprintadded', $request->name);
        
        return Redirect::back();
    }

    /**
     * Activates a sprint - given its machine name, project id, from and to dates
     */
    public function activate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3|max:100',
            'from_date' => 'required|date',
            'to_date' => 'required|date'
            ]);

        $sprintName = $request->name;
        $sprintMachineName = $request->machine_name;
        $projectId = (int) $request->project_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        // @todo refactor to a DB transaction

        // Deactivate current sprint in project
        $activeSprintInProject = Project::findOrFail($projectId)->getActiveSprint();
        if($activeSprintInProject)
        {
            $activeSprintInProject->status_id = SprintStatus::getIdByMachineName('inactive');
            $activeSprintInProject->save();
        }

        // Activate new sprint
        if($sprintMachineName)
        {
            $sprint = Sprint::where('machine_name', '=', $sprintMachineName)
            ->where('machine_name', '!=', 'backlog')
            ->where('project_id', '=', $projectId)
            ->firstOrFail();

            if($sprint)
            {
                $sprint->name = $sprintName;
                $sprint->from_date = $from_date;
                $sprint->to_date = $to_date;
                $sprint->status_id = SprintStatus::getIdByMachineName('active');
                $sprint->sort_order = (int) DB::table('sprints')->max('sort_order') + 1;
                $sprint->save();
                // @todo flash message
            }
        }

        return Redirect::back();
    }

    /**
     * Set the status of a sprint to complete, if all issues in this sprint are complete
     * @param  Request
     * @return array $result
     */
    public function complete(Request $request)
    {
        // default
    	$result = array('message' => 'There was an error processing this request', 
           'status' => 0);

    	$sprintName = $request->sprintMachineName;
        $projectId = $request->projectId;
        $sprint = Sprint::where('machine_name', '=', $sprintName)
        ->where('project_id', '=', $projectId)
        ->firstOrFail();

        if($sprint && $sprint->isComplete())
        {
          $sprint->status_id = SprintStatus::getIdByMachineName('complete');
          $sprint->save();

          $result = array('message' => 'This sprint has been set to complete',
            'sprintMachineName' => $sprintName,
            'status' => 1);
        } 
        else {
            $result = array('message' => 'All issues in the sprint should be complete or archived before setting a sprint to complete',
                'sprintMachineName' => $sprintName,
                'status' => 0);
        }
        return $result;
    }

}
