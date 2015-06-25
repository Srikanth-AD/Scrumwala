<?php namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\IssueRequest;
use App\Http\Requests\IssueSearchRequest;
use App\Issue;
use App\IssueStatus;
use App\IssueType;
use App\Project;
use App\Sprint;
use App\Utils;
use Auth;
use DB;
use Illuminate\Support\Facades\Redirect;
use Request;
use Session;

class IssuesController extends Controller {

	public function __construct() {
		$this->middleware('auth');
	}

	/**
	 * Return a view with issueCount and issues collection
	 *
	 * @return Response
	 */
	public function index() {
		$issuesCount = Issue::latest()->get()->count();
		$issues = DB::table('issues')->orderBy('created_at', 'desc')->paginate(15);

		$issues->each(function ($issue) {
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
	 * Show the form for creating a new issue
	 *
	 * @return Response
	 */
	public function create() {
		$projectNames = Project::lists('name', 'id')->all();
		krsort($projectNames);
		$issueTypeLabels = IssueType::lists('label', 'id')->all();
		krsort($issueTypeLabels);

		return view('issues.create')->with([
			'projectNames' => $projectNames,
			'issueTypeLabels' => $issueTypeLabels,
		]);
	}

	/**
	 * Store a newly created issue in storage.
	 * Redirect to the corresponding project's work view
	 *
	 * @return Response
	 */
	public function store(IssueRequest $request) {
		$todoIssueStatusId = IssueStatus::getIdByMachineName('todo');
		$backlogSprintId = (int) Project::findOrFail($request->project_id)->getBacklogSprint()->id;
		$latestIssueInSprint = Sprint::findOrFail($backlogSprintId)->getLatestIssueInSprint();

		$request['user_id'] = Auth::user()->id;
		$request['sprint_id'] = $backlogSprintId;
		$request['status_id'] = $todoIssueStatusId;

		if ($latestIssueInSprint) {
			$request['sort_prev'] = $latestIssueInSprint->id;
		}

		$issue = Issue::create($request->all());

		// Update sort order for - previously - latest issue
		if (Utils::getIssueCountInSprint($backlogSprintId) > 1) {

			$previouslyLatestIssueInSprint = Issue::findOrFail($latestIssueInSprint->id);
			$previouslyLatestIssueInSprint->sort_next = $issue->id;
			$previouslyLatestIssueInSprint->save();
		}

		return redirect('projects/' . $request->project_id);
	}

	/**
	 * quickAdd Add an issue from project plan view - inline form
	 * @param  IssueRequest $request
	 * @return Response
	 */
	public function quickAdd(IssueRequest $request) {
		$todoIssueStatusId = IssueStatus::getIdByMachineName('todo');
		$backlogSprintId = (int) Project::findOrFail($request->project_id)->getBacklogSprint()->id;
		$latestIssueInSprint = Sprint::findOrFail($backlogSprintId)->getLatestIssueInSprint();

		$request['user_id'] = Auth::user()->id;
		$request['sprint_id'] = $backlogSprintId;
		$request['status_id'] = $todoIssueStatusId;

		if ($latestIssueInSprint) {
			$request['sort_prev'] = $latestIssueInSprint->id;
		}

		$issue = Issue::create($request->all());

		//Update sort order for - previously - latest issue
		if (Utils::getIssueCountInSprint($backlogSprintId) > 1) {

			$previouslyLatestIssueInSprint = Issue::findOrFail($latestIssueInSprint->id);
			$previouslyLatestIssueInSprint->sort_next = $issue->id;
			$previouslyLatestIssueInSprint->save();
		}

		return Redirect::back();
	}

	/**
	 * Return a view to display an issue's details
	 *
	 * @return Response
	 */
	public function show(Issue $issue) {
		return view('issues.show')->with('issue', $issue);
	}

	/**
	 * Return a view for editing an issue's details
	 *
	 * @return Response
	 */
	public function edit(Issue $issue) {
		$projectNames = Project::lists('name', 'id')->all();

		$issueTypeLabels = IssueType::lists('label', 'id')->all();
		krsort($issueTypeLabels);

		$issueStatusLabels = IssueStatus::lists('label', 'id')->all();
		krsort($issueStatusLabels);

		$deadline = ($issue->deadline) ? $issue->deadline->format('Y-m-d') : null;

		return view('issues.edit')->with(
			['issue' => $issue,
				'projectNames' => $projectNames,
				'issueTypeLabels' => $issueTypeLabels,
				'issueStatusLabels' => $issueStatusLabels,
				'deadline' => $deadline,
			]);
	}

	/**
	 * Update an issue in storage.
	 *
	 * @return Response
	 */
	public function update(Issue $issue, IssueRequest $request) {
		$issue->update($request->all());
		Session::flash('issueUpdate', $issue->title);
		return redirect('projects/' . $issue->project_id);
	}

	/**
	 * Return a view to display issue search results for a given query
	 */

	public function search(IssueSearchRequest $request) {
		$query = trim(strip_tags($request->get('query')));
		$issues = Issue::where('title', 'LIKE', "%$query%")->get();

		$issues->each(function ($issue) {
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
			'query' => $query,
		]);
	}

	/**
	 * Update the status of an issue
	 * @return string
	 */
	public function statuschange() {
		$result = 'There was an error updating the issue status';
		$issueStatusMachineNames = IssueStatus::lists('machine_name', 'id')->all();
		$newIssueStatusMachineName = trim(Request::get('machineNameOfNewIssueStatus'));

		if (in_array($newIssueStatusMachineName, $issueStatusMachineNames)) {
			$issueId = (int) trim(Request::get('issueId'));
			$statusId = array_search($newIssueStatusMachineName, $issueStatusMachineNames);

			if (Issue::find($issueId) != NULL) {
				DB::update('update issues set status_id = ? where id = ?', [$statusId, $issueId]);
				$result = 'Issue status has been changed successfully.';

				// If an issue is archived set sort order (previous and next) to NULL
				if ($newIssueStatusMachineName == 'archive') {

					// set sort_prev and sort_next to NULL
					$archivedIssue = Issue::findOrFail($issueId);
					$archivedIssue->sort_prev = NULL;
					$archivedIssue->sort_next = NULL;
					$archivedIssue->save();
				}
			}
		}
		return $result;
	}

	/**
	 * Update the sprint associated with an issue
	 * @todo create a function to get sprint machine names for a given project
	 */
	public function sprintchange() {
		$result = "There was an error updating the issue's sprint association";
		$issueId = (int) trim(Request::get('issueId'));
		$projectId = (int) trim(Request::get('projectId'));
		$issue = Issue::findOrFail($issueId);
		$machineNameOfNewSprint = trim(strip_tags(Request::get('machineNameOfNewSprint')));

		$sprints = Project::find($issue->project_id)->getSprints();

		$sprintMachineNames = [];
		foreach ($sprints as $sprint) {
			array_push($sprintMachineNames, $sprint->machine_name);
		}

		if (in_array($machineNameOfNewSprint, $sprintMachineNames)) {
			$sprintId = (int) Sprint::where('machine_name', '=', $machineNameOfNewSprint)
				->where('project_id', '=', $projectId)->first()->id;
			DB::update('update issues set sprint_id = ? where id = ?', [$sprintId, $issueId]);
			$result = "Issue's sprint association has been updated successfully.";
		}
		return $result;
	}

}
