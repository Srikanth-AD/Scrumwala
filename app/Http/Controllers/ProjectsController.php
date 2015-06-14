<?php namespace App\Http\Controllers;
use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\ProjectRequest;
use Carbon\Carbon;
use App\Project;

class ProjectsController extends Controller {

	public function __construct()
	{
		$this->middleware('auth');
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
		if($activeSprint)
		{
			$issues = Project::find($project->id)->getIssuesFromSprint($activeSprint->id);
		} else {
			$issues = [];
		}

		return view('projects.show')->with([
			'project' => $project,
			'issues' => $issues,
			'sprint' => $activeSprint
		]);

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
	public function update(Project $project, ProjectRequest $request)
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
