<?php namespace App\Http\Controllers;
use Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use \Symfony\Component\Process\Exception\InvalidArgumentException;
use App\Project;
use App\IssueStatus;

class ProjectsController extends Controller {

	protected $issueService;

	public function __construct(\App\Services\IIssueService $issueService)
	{
		$this->middleware('auth');
		$this->issueService = $issueService;
	}

	/**
	 * Return a view to display the list of projects
	 *
	 * @return Response
	 */
	public function index()
	{
		$projects = Project::latest()->get();
		return view('projects.index')->with('projects', $projects);
	}

	/**
	 * Show the form for creating a new project.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('projects.create');
	}

	/**
	 * Store a newly created project in storage.
	 *
	 * @return Response
	 */
	public function store(ProjectRequest $request)
	{
		$project = new Project($request->all());
		Auth::user()->projects()->save($project);
		$project->createBacklogSprint($project->id);
		return redirect('projects');
	}

	/**
	 * Display the specified project.
	 *
	 * @return Response
	 */
	public function show(Project $project)
	{
		$activeSprint = $project->getActiveSprint();
		$projectType = $project->type;
		$issueList = [];
		$numIssues = 0;

		switch ($projectType) {
			case 'scrum':
				if($activeSprint)
				{
					$numIssues = $project->getNumberOfActiveIssues();
					\Log::info('num issues: ' . $numIssues);
					$issueList = $this->issueService->getIssuesByStatusFromSprint($activeSprint->id);
					\Log::info('issues: ' . \json_encode($issueList));
				}
		
				return view('projects.show.scrum')->with([
					'project' => $project,
					'sprint' => $activeSprint,
					'issueList' => $issueList,
					'numIssues' => $numIssues
				]);
				break;

			case 'kanban':
				$numIssues = $project->getNumberOfActiveIssues();
				$issueList = $this->issueService->getIssuesByStatus($project->id);
		
				return view('projects.show.kanban')->with([
					'project' => $project,
					'issueList' => $issueList,
					'numIssues' => $numIssues
				]);
				break;
			
			default:
				throw new InvalidArgumentException("Invalid Project Type", 1);
				break;
		}


	}

	/**
	 * Show the form for editing the specified project.
	 *
	 * @return Response
	 */
	public function edit(Project $project)
	{
		$deadline = ($project->deadline) ? $project->deadline->format('Y-m-d') : null;
		return view('projects.edit')->with([
			'project' => $project,
			'deadline' => $deadline]);
	}

	/**
	 * Update the specified project in storage.
	 *
	 * @return Response
	 */
	public function update(ProjectRequest $request, Project $project)
	{
		$project->update($request->all());
		return redirect('projects');
	}

	/**
	 * Return the project plan view
	 * @param Project $project
	 * @return Response
	 */
	public function plan(Project $project)
	{
		return view('projects.plan')->with([
			'project' => $project
			]);
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
