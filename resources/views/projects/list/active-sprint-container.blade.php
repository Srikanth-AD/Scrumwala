<div class="row container-fluid sprint-stats-container">
    <?php $activeSprint = App\Project::find($project->id)->getActiveSprint() ?>
    @if($activeSprint)
        <h5>
            <span class="grey">Active Sprint:</span>
            {{$activeSprint->name}}
        </h5>
        @if($activeSprint->from_date && $activeSprint->to_date)
            <h5 class="grey">
                <img width="18" height="18" src="{{asset('css/icons/ic_event_grey600_36dp.png')}}"/>
                <span class="grey">From: </span>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $activeSprint->from_date)->format('F d, Y')}}
            </h5>
            <h5 class="grey">
                <img width="18" height="18" src="{{asset('css/icons/ic_schedule_black_36dp.png')}}"/>
                <span class="grey">To: </span>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $activeSprint->to_date)->format('F d, Y')}}
            </h5>
            <?php $issueStatuses = App\IssueStatus::getBySortOrder() ?>
            @foreach($issueStatuses as $issueStatus)
                <div class="col-sm-4">
                    <?php $list = App\Utils::getIssuesInSprintByIssueStatus($issueStatus->machine_name,$activeSprint->id) ?>
                    <div data-status-heading="{{$issueStatus->machine_name}}">{{$issueStatus->label}} <br/>
                        <span class="grey issue-count">({{$list->count()}})</span>
                    </div>                
                </div>
            @endforeach
        @endif
    @endif
</div>