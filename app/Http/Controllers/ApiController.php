<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Issue;
use App\Project;
use App\IssueType;
use App\IssueStatus;
class ApiController extends Controller {

//	public function issues()
//	{
//		$issues = Issue::paginate(5)->getCollection([
//			'id',
//			'title',
//			'project_id',
//			'status_id',
//			'type_id']);
//		//return $issues;
//		$issues->each(function($issue)
//		{
//			$issue->id = (int) $issue->id;
//			$issue->projectName = Project::find($issue->project_id)->name;
//			$issue->statusLabel = IssueStatus::find($issue->status_id)->label;
//			$issue->typeLabel = IssueType::find($issue->type_id)->label;
//			unset($issue->project_id);
//			unset($issue->status_id);
//			unset($issue->type_id);
//		});
//		return $issues;
//	}

}
