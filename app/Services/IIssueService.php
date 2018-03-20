<?php

namespace App\Services;

/**
 * Issue Service interface
 * 
 * @author Craig Weiser <craig.weiser@weiserwebdev.com>
 * @created 14 April 2017
 */
interface IIssueService
{
    public function reorder($reorderedIssue, $newNextIssue);
    public function getIssuesByStatus($projectId);
    public function getIssuesByStatusFromSprint($sprintId);
}
