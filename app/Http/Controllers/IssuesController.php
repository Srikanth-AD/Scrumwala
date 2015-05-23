<?php namespace App\Http\Controllers;
use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\Pagination;
//use Illuminate\Http\Request;
use Request;
use App\Http\Requests\IssueRequest;
use App\Http\Requests\IssueSearchRequest;
use Carbon\Carbon;
use App\Issue;
use App\Sprint;
use App\Project;
use App\IssueType;
use App\IssueStatus;
use DB;
use Session;
class IssuesController extends Controller {

	public function __construct()
	{
		$this->middleware('auth');
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$issuesCount = Issue::latest()->get()->count();
        $issues = DB::table('issues')->orderBy('created_at', 'desc')->paginate(15);

		$issues->each(function($issue)
		{
			$issue->id = (int) $issue->id;
			$issue->projectName = Project::find($issue->project_id)->name;
			$issue->statusLabel = IssueStatus::find($issue->status_id)->label;
			$issue->typeLabel = IssueType::find($issue->type_id)->label;
			unset($issue->project_id);
			unset($issue->status_id);
			unset($issue->type_id);
		});

		return view('issues.index')->with([
			'issuesCount' => $issuesCount,
			'issues' => $issues]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$projectNames = Project::lists('name', 'id');
		krsort($projectNames);
		$issueTypeLabels = IssueType::lists('label','id');
		krsort($issueTypeLabels);

		return view('issues.create')->with([
			'projectNames' => $projectNames,
			'issueTypeLabels' => $issueTypeLabels,
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(IssueRequest $request)
	{
		$todoIssueStatusId = IssueStatus::getIdByMachineName('todo');
		$request['user_id'] = Auth::user()->id;
		$issue = new Issue;
		$request['sprint_id'] = Project::find($request->project_id)->getBacklogSprint()->id;
		$request['status_id'] = $todoIssueStatusId;
		$issue->create($request->all());
		return redirect('projects/' . $request->project_id);
	}

	/**
	 * Display the specified resource.
	 *
	 * @return Response
	 */
	public function show(Issue $issue)
	{
		return view('issues.show')->with('issue', $issue);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @return Response
	 */
	public function edit(Issue $issue)
	{
		$projectNames = Project::lists('name', 'id');
		$issueTypeLabels = IssueType::lists('label','id');
		krsort($issueTypeLabels);
		$issueStatusLabels = IssueStatus::lists('label','id');
		krsort($issueStatusLabels);
		$deadline = ($issue->deadline) ? $issue->deadline->format('Y-m-d') : null;

		return view('issues.edit')->with(
			['issue' => $issue,
				'projectNames' => $projectNames,
				'issueTypeLabels' => $issueTypeLabels,
				'issueStatusLabels' => $issueStatusLabels,
				'deadline' => $deadline
			]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @return Response
	 */
	public function update(Issue $issue, IssueRequest $request)
	{
		$issue->update($request->all());
		Session::flash('issueUpdate', $issue->title);
		return redirect('projects/' . $issue->project_id);
	}

	public function search(IssueSearchRequest $request)
	{
		$query = trim(strip_tags($request->get('query')));
		$issues = Issue::where('title', 'LIKE', "%$query%")->get();

        $issues->each(function($issue)
		{
			$issue->id = (int) $issue->id;
			$issue->projectName = Project::find($issue->project_id)->name;
			$issue->statusLabel = IssueStatus::find($issue->status_id)->label;
			$issue->typeLabel = IssueType::find($issue->type_id)->label;
			unset($issue->project_id);
			unset($issue->status_id);
			unset($issue->type_id);
		});

        return view('issues.searchresults')->with([
			'issues' => $issues,
			'query' => $query
		]);
	}

	/**
	 * Update the status of an issue
	 * @return string
	 */
	public function statuschange()
	{
		$result = 'There was an error updating the issue status';
		$issueStatusMachineNames = IssueStatus::lists('machine_name','id');
		$newIssueStatusMachineName = trim(Request::get('machineNameOfNewIssueStatus'));

		if(in_array($newIssueStatusMachineName, $issueStatusMachineNames))
		{
			$issueId = (int) trim(Request::get('issueId'));
			$statusId = array_search($newIssueStatusMachineName, $issueStatusMachineNames);

			if(Issue::find($issueId) != NULL)
			{
				DB::update('update issues set status_id = ? where id = ?', [$statusId, $issueId]);
				$result = 'Issue status has been changed successfully.';
			}
		}
		return $result;
	}

	/**
	 * Update the sprint associated with an issue
	 * @todo create a function to get sprint machine names for a given project
	 */
	public function sprintchange()
	{
		$result = "There was an error updating the issue's sprint association";
		$issueId = (int) trim(Request::get('issueId'));
		$projectId = (int) trim(Request::get('projectId'));
		$issue = Issue::find($issueId);
		$machineNameOfNewSprint = trim(strip_tags(Request::get('machineNameOfNewSprint')));

		$sprints = Project::find($issue->project_id)->getSprints();

		$sprintMachineNames = [];
		foreach($sprints as $sprint)
		{
			array_push($sprintMachineNames, $sprint->machine_name);
		}

		if(in_array($machineNameOfNewSprint, $sprintMachineNames))
		{
			$sprintId = (int) Sprint::where('machine_name', '=', $machineNameOfNewSprint)
								->where('project_id', '=', $projectId)->first()->id;
			DB::update('update issues set sprint_id = ? where id = ?', [$sprintId, $issueId]);
			$result = "Issue's sprint association has been updated successfully.";
		}
		return $result;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
