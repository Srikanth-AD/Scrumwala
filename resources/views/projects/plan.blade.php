@extends('app')

@section('notifications')
    @if (Session::has('sprintadded'))
        <div class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"
                    aria-label="Close"><span aria-hidden="true">&times;</span>
            </button>
            Sprint: {{ Session::get('sprintadded') }} has been added.
        </div>
    @endif
@endsection

@section('content')
    @if($project)
        <div class="container-fluid">
            <h2 data-id="{{$project->id}}" id="project-name" class="project-title">
                Plan: {{$project->name}}
            </h2>
            @if( $project->deadline)
                <img alt="deadline" title="deadline" width="18" height="18" src="{{asset('css/icons/ic_schedule_black_36dp.png')}}"/>
                <span>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $project->deadline)->diffForHumans()}}</span>
            @endif
            <div class="actions">
                <div class="col-md-4">
                    <h5 class="group-heading">Navigate</h5>
                    <a href="/projects/{{$project->id}}" class="btn btn-default">
                        Work <img width="18" height="18" alt="navigate to work mode"
                             src="{{asset('css/icons/ic_arrow_forward_grey600_36dp.png')}}" />
                    </a>
                </div>
                <div class="col-md-3">
                </div>
                <div class=" col-md-3">
                    <h5 class="group-heading">Actions</h5>
                    <div class="btn-group" role="group">
                        <button id="action-add-issue" type="button" class="btn btn-default">
                            <img alt="add issue" width="18" height="18"
                                 src="{{asset('css/icons/ic_add_black_36dp.png')}}" />
                            Add Issue
                        </button>
                        <button id="action-add-sprint" type="button" class="btn btn-default">
                            <img alt="add sprint" width="18" height="18"
                                 src="{{asset('css/icons/ic_add_black_36dp.png')}}" />
                            Add Sprint
                        </button>
                    </div>
                </div>
            </div>

                <div class="col-md-12" id="action-add-sprint-body">
                    <div class="col-md-8">
                        @include('projects.plan.sprints-add-form')
                        <button type="button" class="close" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="col-md-12" id="action-add-issue-body-dialog">
                    @include('projects.plan.issue-add-form')
                </div>
            </div>
            <div class="col-md-12 project-plan main-content">
                <div class="container-fluid col-md-10">
                    @foreach(App\Project::find($project->id)->getSprints() as $sprint)
                        <div class="row sprint-header"
                            data-machine-name="{{$sprint->machine_name}}">
                            <div class="col-md-4">

                                <h3 class="sprint-name"
                                    data-machine-name="{{$sprint->machine_name}}">
                                    <img class="toggle" width="18" height="18"
                                    src="{{asset('css/icons/ic_keyboard_arrow_right_black_36dp.png')}}" />
                                    {{$sprint->name}}
                                    <span class="grey issue-count">
                                        ({{App\Utils::getIssueCountInSprint($sprint->id)}})
                                    </span>
                                    @if($sprint->status_id == App\SprintStatus::getIdByMachineName('active'))
                                        <span class="badge">Active Sprint</span>
                                    @endif
                                </h3>

                            </div>
                            <div class="col-md-4">
                            </div>
                            <div class="col-md-4 date-range">
                                @if($sprint->from_date && $sprint->to_date &&
                                    $sprint->machine_name != 'backlog' && $sprint->status_id)
                                    <div class="col-md-6">
                                        <span><strong>From: </strong></span> {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sprint->from_date)->format('F d, Y')}}
                                    </div>
                                    <div class="col-md-6">
                                        <span><strong>To: </strong></span> {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sprint->to_date)->format('F d, Y')}}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if($sprint->machine_name != 'backlog' && $sprint->status_id != App\SprintStatus::getIdByMachineName('active'))
                            <button data-status="0" data-id="{{$sprint->machine_name}}"
                                    class="btn btn-default btn-sm sprint-activate"
                                    type="submit">Activate
                            </button>
                            @include('projects.plan.sprint-activate-form')
                        @endif
                        @if($sprint->status_id == App\SprintStatus::getIdByMachineName('active'))
                            <button data-status="0" data-id="{{$sprint->machine_name}}"
                                    data-project-id="{{$project->id}}"
                                    class="btn btn-default btn-sm sprint-complete"
                                    type="submit">Complete
                            </button>
                        @endif
                        @include('projects.plan.issues-in-sprint')
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection
@include('projects.plan.js')
