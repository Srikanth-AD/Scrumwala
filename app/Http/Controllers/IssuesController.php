<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\IssueRequest;
use App\Http\Requests\IssueSearchRequest;
use App\Issue;
use App\IssueStatus;
use App\IssueType;
use App\Project;
use App\Sprint;
use Auth;
use DB;
use Illuminate\Support\Facades\Redirect;
use Request;
use Response;
use Session;
use App\Services\IssueService as IssueService;

class IssuesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Return a view with issueCount and issues collection
     *
     * @return Response
     */
    public function index()
    {
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
    public function create()
    {
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
    public function store(IssueRequest $request)
    {
        $issue = $this->addNewIssue($request);
        if (empty($issue)) {
            return back()->withInput()->withErrors();
        }
        return redirect('projects/' . $request->project_id);
    }

    /**
     * quickAdd Add an issue from project plan view - inline form
     * @param  IssueRequest $request
     * @return Response
     */
    public function quickAdd(IssueRequest $request)
    {
        $issue = $this->addNewIssue($request);
        if (empty($issue)) {
            return back()->withInput()->withErrors();
        }
        return Redirect::back();
    }

    /**
     * Return a view to display an issue's details
     *
     * @return Response
     */
    public function show(Issue $issue)
    {
        return view('issues.show')->with('issue', $issue);
    }

    /**
     * Return a view for editing an issue's details
     *
     * @return Response
     */
    public function edit(Issue $issue)
    {
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
    public function update(Issue $issue, IssueRequest $request)
    {
        $issue->update($request->all());
        Session::flash('issueUpdate', $issue->title);
        return redirect('projects/' . $issue->project_id);
    }

    /**
     * Return a view to display issue search results for a given query
     */
    public function search(IssueSearchRequest $request)
    {
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
    public function statuschange()
    {
        $result = 'There was an error updating the issue status';
        $issueStatusMachineNames = IssueStatus::lists('machine_name', 'id')->all();
        $newIssueStatusMachineName = trim(strip_tags(Request::get('machineNameOfNewIssueStatus')));

        $prevIssueId = trim(strip_tags(Request::get('prevIssueId')));
        $nextIssueId = trim(strip_tags(Request::get('nextIssueId')));

        if (in_array($newIssueStatusMachineName, $issueStatusMachineNames)) {
            $issueId = (int) trim(Request::get('issueId'));
            $statusId = array_search($newIssueStatusMachineName, $issueStatusMachineNames);

            $issue = Issue::findOrFail($issueId);
            if ($issue) {
                DB::update('update issues set status_id = ? where id = ?', [$statusId, $issueId]);
                $result = 'Issue status has been changed successfully.';

                // If an issue is archived set sort order (previous and next) to NULL
                if ($newIssueStatusMachineName == 'archive') {

                    // Check if previous and next issue ids provided in the Request exist in the sprint
                    if ($prevIssueId &&
                                    Sprint::find($issue->sprint_id)->issues()
                                    ->where('id', '=', $issue->id)->first()->id) {
                        // do nothing
                    } else {
                        $prevIssueId = NULL;
                    }

                    if ($nextIssueId &&
                                    Sprint::find($issue->sprint_id)->issues()
                                    ->where('id', '=', $issue->id)->first()->id) {
                        // do nothing
                    } else {
                        $nextIssueId = NULL;
                    }

                    // set sort order for previous and next issues in the same sprint
                    if ($prevIssueId) {
                        $prevIssue = Issue::findOrFail($prevIssueId);
                        $prevIssue->sort_next = $issue->sort_next ? $issue->sort_next : NULL;
                        $prevIssue->save();
                    }

                    if ($nextIssueId) {
                        $nextIssue = Issue::findOrFail($nextIssueId);
                        $nextIssue->sort_prev = $issue->sort_prev ? $issue->sort_prev : NULL;
                        $nextIssue->save();
                    }

                    // set sort_prev and sort_next for archived issue to NULL
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
    public function sprintchange()
    {
        $result = "There was an error updating the issue's sprint association";
        $issueId = (int) trim(Request::get('issueId'));
        $projectId = (int) trim(Request::get('projectId'));
        $issue = Issue::findOrFail($issueId);
        $currentSprintIdOfIssue = $issue->sprint_id;
        $machineNameOfNewSprint = trim(strip_tags(Request::get('machineNameOfNewSprint')));
        $nextIssueIdInNewSprint = trim(strip_tags(Request::get('nextIssueId')));
        $prevIssueIdInNewSprint = trim(strip_tags(Request::get('prevIssueId')));

        $sprints = Project::findOrFail($issue->project_id)->getSprints();

        $sprintMachineNames = [];
        foreach ($sprints as $sprint) {
            array_push($sprintMachineNames, $sprint->machine_name);
        }

        if (in_array($machineNameOfNewSprint, $sprintMachineNames)) {
            $newSprintId = (int) Sprint::where('machine_name', '=', $machineNameOfNewSprint)
                            ->where('project_id', '=', $projectId)->first()->id;

            // update sort order for previous and next issues in current sprint (association)
            $prevIssue = Sprint::findOrFail($currentSprintIdOfIssue)->getPreviousIssueBySortOrder($issueId);
            $nextIssue = Sprint::findOrFail($currentSprintIdOfIssue)->getNextIssueBySortOrder($issueId);

            if ($prevIssue) {
                $prevIssue->sort_next = $issue->sort_next ? $issue->sort_next : NULL;
                $prevIssue->save();
            }

            if ($nextIssue) {
                $nextIssue->sort_prev = $issue->sort_prev ? $issue->sort_prev : NULL;
                $nextIssue->save();
            }

            // Check if previous and next issue ids provided in the Request exist in the new sprint
            if ($prevIssueIdInNewSprint &&
                            Sprint::find($newSprintId)->issues()
                            ->where('id', '=', $prevIssueIdInNewSprint)->first()->id) {
                // update sort order for previous issue in new sprint
                $prevIssueInNewSprint = Issue::findOrFail($prevIssueIdInNewSprint);
                $prevIssueInNewSprint->sort_next = $issueId;
                $prevIssueInNewSprint->save();
            } else {
                $prevIssueIdInNewSprint = NULL;
            }

            if ($nextIssueIdInNewSprint &&
                            Sprint::find($newSprintId)->issues()
                            ->where('id', '=', $nextIssueIdInNewSprint)->first()->id) {
                // update sort order for next issue in new sprint
                $nextIssueInNewSprint = Issue::findOrFail($nextIssueIdInNewSprint);
                $nextIssueInNewSprint->sort_prev = $issueId;
                $nextIssueInNewSprint->save();
            } else {
                $nextIssueIdInNewSprint = NULL;
            }

            // Update sprint association and sort previous and next for issue
            DB::update('update issues set sprint_id = ?, sort_prev = ?, sort_next = ? where id = ?', [$newSprintId, $prevIssueIdInNewSprint, $nextIssueIdInNewSprint, $issueId]);

            $result = "Issue's sprint association has been updated successfully.";
        }
        return $result;
    }

    public function sortOrderPriority()
    {
        $issueId = (int) trim(Request::get('issueId'));
        $projectId = (int) trim(Request::get('projectId'));
        if (Request::get('newNextIssueId')) {
            $newNextIssueId = trim(strip_tags(Request::get('newNextIssueId')));
        } else {
            $newNextIssueId = NULL; // have been moved to the bottom of the list
        }

        $issue = Issue::findOrFail($issueId);
        $newNextIssue = Issue::findOrFail($newNextIssueId);
        $issueService = new IssueService(new Issue());
        $result = $issueService->reorder($issue, $newNextIssue);
        if ($result) {
            return Response::json(['success' => 'Reorder successfull'], 200); // Status code here
        }
        return Response::json(['error' => 'Reorder failed!'], 500); // Status code here
    }

    /**
     * sortorder Set sort order (sort_prev and sort_next) for an issue when its dragged and dropped into
     * the same sprint
     * @return $result array
     */
    public function sortorderOdd()
    {
        $result = "There was an error updating the issue's sort order";
        $issueId = (int) trim(Request::get('issueId'));
        $projectId = (int) trim(Request::get('projectId'));

        if (Request::get('newPrevIssueId')) {
            $newPrevIssueIdInSprint = trim(strip_tags(Request::get('newPrevIssueId')));
        } else {
            $newPrevIssueIdInSprint = NULL;
        }

        if (Request::get('newNextIssueId')) {
            $newNextIssueIdInSprint = trim(strip_tags(Request::get('newNextIssueId')));
        } else {
            $newNextIssueIdInSprint = NULL;
        }

        // @todo check if the above prev. and next issues are actually in the same sprint as issue

        $issue = Issue::findOrFail($issueId);

        // Update sort order for current previous and next issues in the sprint
        $currentPrevIssue = Sprint::findOrFail($issue->sprint_id)->getPreviousIssueBySortOrder($issueId);
        $currentNextIssue = Sprint::findOrFail($issue->sprint_id)->getNextIssueBySortOrder($issueId);

        if ($currentPrevIssue) {
            $currentPrevIssue->sort_next = $issue->sort_next ? $issue->sort_next : NULL;
            $currentPrevIssue->save();
        }

        if ($currentNextIssue) {
            $currentNextIssue->sort_prev = $issue->sort_prev ? $issue->sort_prev : NULL;
            $currentNextIssue->save();
        }

        // Update sort order for new previous and next issues in the sprint
        if ($newPrevIssueIdInSprint) {
            $newPrevIssue = Issue::findOrFail($newPrevIssueIdInSprint);
            $newPrevIssue->sort_next = $issueId;
            $newPrevIssue->save();
        }

        if ($newNextIssueIdInSprint) {
            $newNextIssue = Issue::findOrFail($newNextIssueIdInSprint);
            $newNextIssue->sort_prev = $issueId;
            $newNextIssue->save();
        }

        // Update sort previous and next for issue
        DB::update('update issues set sort_prev = ?, sort_next = ? where id = ?', [$newPrevIssueIdInSprint, $newNextIssueIdInSprint, $issueId]);

        $result = "Issue's sort order has been updated successfully.";
        return $result;
    }

    private function addNewIssue(IssueRequest $request)
    {
        if (empty($request)) {
            throw new \Exception('Request for new issue is empty!');
        }
        $todoIssueStatusId = IssueStatus::getIdByMachineName('todo');

        $backlogsprint = Project::findOrFail($request->project_id)->getBacklogSprint();

        if (empty($backlogsprint)) {
            return null;
        }

        $backlogSprintId = (int) $backlogsprint->id;
        //$latestIssueInSprint = Sprint::findOrFail($backlogSprintId)->getLatestIssueInSprint();

        $issueService = new IssueService(new Issue());
        $priorityOrder = $issueService->getLastPriorityOrder();


        $request['user_id'] = Auth::user()->id;
        $request['sprint_id'] = $backlogSprintId;
        $request['status_id'] = $todoIssueStatusId;

        if ($priorityOrder) {
            //$request['sort_prev'] = $latestIssueInSprint->id;
            $priorityOrder++;
        } else {
            $priorityOrder = 1;
        }
        $request['priority_order'] = $priorityOrder;


        $issue = Issue::create($request->all());

        return $issue;
    }

}
