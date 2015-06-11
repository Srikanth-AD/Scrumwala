<?php namespace App\Http\Controllers;
use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\IssueRequest;
use Carbon\Carbon;
use App\Issue;
use App\Project;
use App\IssueType;
use App\IssueStatus;
class IssueStatusesController extends Controller {

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
		$issuestatuses = IssueStatus::all();
		return $issuestatuses;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$projectNames = Project::lists('name', 'id')->all();
		krsort($projectNames);
		$issueTypeLabels = IssueType::lists('label','id')->all();
		krsort($issueTypeLabels);
		$issueStatusLabels = IssueStatus::lists('label','id')->all();
		krsort($issueStatusLabels);

		return view('issues.create')->with([
			'projectNames' => $projectNames,
			'issueTypeLabels' => $issueTypeLabels,
			'issueStatusLabels' => $issueStatusLabels
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(IssueRequest $request)
	{       
		$request['user_id'] = Auth::user()->id;
		$issue = new Issue;
		$request['sprint_id'] = 17;
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
		$projectNames = Project::lists('name', 'id')->all();
		$issueTypeLabels = IssueType::lists('label','id')->all();
		krsort($issueTypeLabels);
		$issueStatusLabels = IssueStatus::lists('label','id')->all();
		krsort($issueStatusLabels);

		return view('issues.edit')->with(
			['issue' => $issue,
				'projectNames' => $projectNames,
				'issueTypeLabels' => $issueTypeLabels,
				'issueStatusLabels' => $issueStatusLabels
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
		return redirect('projects/' . $issue->project_id);
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
