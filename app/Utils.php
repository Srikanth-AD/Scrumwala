<?php
namespace App;
use DB;
class Utils {

    /**
     * getIssueCountByStatus Get the number of issues in a given project, filtered by a issue type
     * @param int $projectId
     * @param string $issueStatusLabel
     * @return array $count
     */
    public static function getIssueCountByStatus($projectId, $issueStatusLabel)
    {
        $issueStatusId = IssueStatus::where('label', '=', $issueStatusLabel)->first()->id;
        $count = ['count' => 0, 'percentage' => 0];

        $count['count'] = DB::table('issues')
        ->select(['id'])
        ->where(['project_id' =>  $projectId, 'status_id' => $issueStatusId])
        ->count();
        if($count['count'] > 0)
        {
            $count['percentage'] = number_format((($count['count'] / self::getIssueCountInProject($projectId)) * 100), 0);
        }
        return $count;
    }

    /**
     * [getIssueCountInSprintByStatus Get issue count in sprint by issue status]
     * @param  int $projectId
     * @param  int $sprintId
     * @param  string $issueStatusMachineName
     * @return array $count
     */
    public static function getIssueCountInSprintByStatus($projectId, $sprintId, $issueStatusMachineName)
    {
        $issueStatusId = IssueStatus::where('machine_name', '=', $issueStatusMachineName)->first()->id;
        $count = ['count' => 0, 'percentage' => 0];

        $count['count'] = DB::table('issues')
        ->select(['id','sprint_id','status_id'])
        ->where(['project_id' =>  $projectId, 'sprint_id' => $sprintId, 'status_id' => $issueStatusId])
        ->count();

        if($count['count'] > 0)
        {
            $count['percentage'] = number_format((($count['count'] / self::getIssueCountInSprint($sprintId)) * 100), 0);
        }
        return $count;
    }


    /**
     * Get the number of issues in a sprint, where issue status is not "archive"
     * @param $sprintId
     * @return int
     */
    public static function getIssueCountInSprint($sprintId)
    {
        return Issue::where('sprint_id', '=', $sprintId)
        ->where('status_id', '!=', IssueStatus::getIdByMachineName('archive'))
        ->count();
    }


    /**
     * Get the number of issues in a project, where issue status is not "archive"
     * @param $projectId
     * @return int
     */
    public static function getIssueCountInProject($projectId)
    {
        return Issue::where('project_id', '=', $projectId)
        ->where('status_id', '!=', IssueStatus::getIdByMachineName('archive'))
        ->count();
    }

    /**
     * Get the list of issues in a sprint by issue status
     * @param string $issueStatusMachineName
     * @param int $sprintId
     */
    public static function getIssuesInSprintByIssueStatus($issueStatusMachineName, $sprintId)
    {
        $statusId = IssueStatus::getIdByMachineName($issueStatusMachineName);
        if($statusId)
        {
            return Issue::where('sprint_id', '=', $sprintId)
            ->where('status_id', '=', $statusId)->get();
        }
        else {
            return false;
        }
    }

}
