<div class="row plan-bar-title">
    <div class="col-md-12">
        <h2 class="project-title">{{$project->name}}</h2>
    </div>
</div>
<div class="row container-fluid plan-bar">
    <div class="col-md-4">
        @if(isset($sprint))
        <h3><span class="grey">Sprint:</span> {{$sprint->name}}</h3>
        @endif
    </div>
    <div class="col-md-4 date-range">
        @if(isset($sprint)) @if($sprint->to_date)
        <img width="18" height="18" src="{{asset('css/icons/ic_schedule_black_36dp.png')}}" />
        <span class="grey">Deadline: </span> {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sprint->to_date)->diffForHumans()}}
        @endif @endif
    </div>
    <div class="col-md-4">
        <div class="btn-group" role="group">
            <a href="/projects/{{$project->id}}/plan" class="btn btn-default">
                <img width="18" height="18" alt="navigate to plan mode"
                        src="{{asset('css/icons/ic_arrow_back_grey600_36dp.png')}}" />
                Plan
            </a>
            @if(!isset($sprint))
                <button id="action-add-issue" type="button" class="btn btn-default">
                    <img alt="add issue" width="18" height="18"
                        src="{{asset('css/icons/ic_add_black_36dp.png')}}" />
                    Add Issue
                </button>
            @endif
        </div>
    </div>
</div>