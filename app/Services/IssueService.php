<?php

namespace App\Services;

use Illuminate\Support\Facades\DB as DB;
use App\IssueStatus as IssueStatus;
use App\Issue as Issue;

/**
 * Handles actions with issues
 *
 * @author Craig Weiser <craig.weiser@weiserwebdev.com>
 * @created 14 April 2017
 * 
 */
class IssueService implements IIssueService
{
    private $_issue;
//    private $_db;
    
//    public function __construct(\App\Issue $issue, \Illuminate\Database\DatabaseManager $db)
    public function __construct(\App\Issue $issue)
    {
        $this->_issue = $issue;
//        $this->_db = $db;
    }
    
    public function getLastPriorityOrder()
    {
        return $this->_issue->max('priority_order');
    }

    public function reorder($reorderedIssue, $newNextIssue)
    {
        $moveUp = $reorderedIssue->priority_order > $newNextIssue->priority_order;
        if ($moveUp) {
            return $this->reorderDesc($reorderedIssue, $newNextIssue);
        }
        return $this->reorderAsc($reorderedIssue, $newNextIssue);
    }

    public function reorderDesc($reorderedIssue, $newNextIssue)
    {
        \Log::info('reorderDesc started...');
        $issuesToChange = $this->_issue->where('priority_order', '>=', $newNextIssue->priority_order)
                ->where('priority_order', '<', $reorderedIssue->priority_order)
                ->get();
        if (empty($issuesToChange)) {
            throw new Exception('No issues found to change the order of');
        }
        try {
            DB::beginTransaction();
            foreach ($issuesToChange as $issueToChange) {
                $issueToChange->priority_order++;
                $issueToChange->save();
            }
            $reorderedIssue->priority_order = $newNextIssue->priority_order;
            $reorderedIssue->save();
            DB::commit();
            \Log::info('reorderDesc finished...');
            return true;
        } catch (\Exception $e) {
            \Log::error('An error occured changeing the proirity order of issues: ' . $e->getMessage());
            DB::rowllback();
            return false;
        }
    }

    public function reorderAsc($reorderedIssue, $newNextIssue)
    {
        \Log::info('reorderAsc started...');
        $issuesToChange = $this->_issue->where('priority_order', '<=', $newNextIssue->priority_order)
                ->where('priority_order', '>', $reorderedIssue->priority_order)
                ->get();
        if (empty($issuesToChange)) {
            throw new Exception('No issues found to change the order of');
        }
        try {
            DB::beginTransaction();
            foreach ($issuesToChange as $issueToChange) {
                $issueToChange->priority_order--;
                $issueToChange->save();
            }
            $reorderedIssue->priority_order = $newNextIssue->priority_order;
            $reorderedIssue->save();
            DB::commit();
            \Log::info('reorderDesc finished...');
            return true;
        } catch (\Exception $e) {
            \Log::error('An error occured changeing the proirity order of issues: ' . $e->getMessage());
            DB::rowllback();
            return false;
        }
    }

    public function getIssuesByStatus($projectId) {
        $issueStatuses = IssueStatus::getBySortOrder();
        $issueList = [];
        foreach($issueStatuses as $issueStatus) {
            $statusId = IssueStatus::getIdByMachineName($issueStatus->machine_name);
            $issueList[$issueStatus->machine_name] = Issue::with('issueType')
            ->where('project_id', '=', $projectId)
            ->where('status_id', '=', $statusId)
            ->orderBy('priority_order')
            ->get();
        }
        return $issueList;
    }

    public function getIssuesByStatusFromSprint($sprintId) {
        $issueStatuses = IssueStatus::getBySortOrder();
        $issueList = [];
        foreach($issueStatuses as $issueStatus) {
            $statusId = IssueStatus::getIdByMachineName($issueStatus->machine_name);
            $issueList[$issueStatus->machine_name] = Issue::with('issueType')
            ->where('sprint_id', '=', $sprintId)
            ->where('status_id', '=', $statusId)
            ->orderBy('priority_order')
            ->get();
        }
        return $issueList;
    }

}
