<ul id="{{$sprint->machine_name}}" class="connectedSortable list-unstyled sprint-list">
    @foreach(App\Project::find($project->id)->getIssuesFromSprint($sprint->id) as $issue)
        <li class="ui-state-default" data-id="{{$issue->id}}">
            <a href="/issues/{{$issue->id}}">
                <span class="issue-id">#{{$issue->id}}</span>
                <span class="@if(App\IssueStatus::find($issue->status_id)->label  == 'Complete') strikethrough @endif">
                    {{$issue->title}}
                </span>
            </a>
            <div class="row issue-actions-attributes">
                <div class="col-md-4 issue-actions">
                    <div class="btn-group pull-left">
                        <button class="btn btn-default">
                            <a href="/issues/{{$issue->id}}/edit">Edit</a>
                        </button>
                        <button type="button" class="btn btn-default archive-issue">Archive</button>
                    </div>
                </div>
                <div class="col-md-4">

                </div>
                <div class="col-md-4">
                    <div class="btn-group pull-right">
                        @if($issue->deadline)
                            <span class="issue-deadline">
                                <img alt="deadline" title="deadline" width="18" height="18" src="{{asset('css/icons/ic_schedule_black_36dp.png')}}"/>
                                <span>
                                    {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $issue->deadline)->diffForHumans()}}
                                </span>
                            </span>
                        @endif
                        <span class="issue-type {{App\IssueType::findOrFail($issue->type_id)->machine_name}}">
                            {{App\IssueType::findOrFail($issue->type_id)->label}}
                        </span>
                        <span class="issue-status {{App\IssueStatus::findOrFail($issue->status_id)->machine_name}}">
                            {{App\IssueStatus::findOrFail($issue->status_id)->label}}
                        </span>
                    </div>
                </div>
            </div>
        </li>
    @endforeach
</ul>