<ul id="{{$status}}" class="connectedSortable list-unstyled sprint-list">
    @foreach($issues as $issue)
        <li class="ui-state-default" data-id="{{$issue->id}}">
            <a href="/issues/{{$issue->id}}">
                <span class="issue-id">#{{$issue->id}}</span>
                <span @if($status == 'complete') class="strikethrough" @endif>
                    {{$issue->title}}
                </span>
            </a>
            @include('issues.issue.deadline')
            <div class="row">
                <div class="col-md-offset-6 col-md-6">
                    <div class="btn-group pull-right">
                        <span class="issue-type {{$issue->issueType->machine_name}}">
                            {{$issue->issueType->label}}
                        </span>
                    </div>
                </div>
            </div>
        </li>
    @endforeach
</ul>