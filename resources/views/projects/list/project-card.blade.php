<div class="project-card">
    <div class="header">
        <h4>
            <a class="project-title" href="{{url('/projects', $project->id)}}">{{ $project->name }}</a>
        </h4>
        <h5>
            Type: {{$project->type}}
        </h5>
        <h5>
            <img width="18" height="18" src="{{asset('css/icons/ic_event_grey600_36dp.png')}}"/>
            <span class="date date-created-at">Created on: {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$project->created_at)->format('F d, Y')}}</span>
        </h5>
        @if($project->deadline)
            <span class="card-stat-icon glyphicon glyphicon-time" aria-hidden="true"></span>
            <span class="deadline">Deadline: {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $project->deadline)->diffForHumans()}}</span>
        @endif
    </div>
    <div class="content">
        @include('projects.list.active-sprint-container')
    </div>
    <div class="row container-fluid">
        <hr/>
        <div class="project-card-actions">
            <span class="small">
                <a href="/projects/{{$project->id}}/edit">Edit</a>
            </span>
        </div>
    </div>
</div>