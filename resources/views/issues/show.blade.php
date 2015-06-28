@extends('app')
@section('content')
    @if($issue)
        <div class="container-fluid col-md-4">
            <div class="clearfix container-fluid main-content">
                <h3 id="issue-title" data-id="{{$issue->id}}">{{$issue->title}}</h3>
                <a href="/issues/{{$issue->id}}/edit">Edit</a>
                <p>Description: {{$issue->description}}</p>
                <p>Status: {{App\IssueStatus::find($issue->status_id)->label}}</p>
                <?php $issueDeadline = $issue->deadline;?>
                @if($issueDeadline)
                    Deadline:   {{$issueDeadline->year}}-{{$issueDeadline->month}}-{{$issueDeadline->day}}
                @endif
                <p>Project: {{App\Project::find($issue->project_id)->name}}</p>
            </div>
            <div id="action-messages" class="container-fluid">
            </div>
        </div>
    @endif
@endsection