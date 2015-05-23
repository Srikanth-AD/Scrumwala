@extends('app')
@section('content')
    @if(count($projects) > 0)
        <div class="fluid-container">
            <h3>Projects</h3>
            <hr/>
            <div class="row fluid-container main-content">
                <div class="fluid-container col-sm-10">
                    @foreach(array_chunk($projects->all(),3) as $row)
                        <div class="row">
                            @foreach($row as $project)
                                <?php
                                $todoCount = App\Utils::getIssueCountByStatus($project->id, 'To Do');
                                $inprogressCount = App\Utils::getIssueCountByStatus($project->id, 'In Progress');
                                $completeCount = App\Utils::getIssueCountByStatus($project->id, 'Complete');
                                ?>
                                <div class="col-sm-4">
                                    <div class="project-card">
                                        <div class="header">
                                            <h4>
                                                <a class="project-title" href="{{url('/projects', $project->id)}}">{{ $project->name }}</a>
                                            </h4>
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
                                                        @endif
                                                @endif
                                                @if($activeSprint)
                                                <div class="row">
                                                        <div class="col-md-4 sprint-stat stat-todo">
                                                            @if($activeSprint)
                                                                    <?php $todosInSprint = App\Utils::getIssueCountInSprintByStatus($project->id, $activeSprint->id, 'todo');
                                                                $todoSprintCount = $todosInSprint['count'];
                                                                $todoSprintPercentage = $todosInSprint['percentage'];
                                                                    ?>
                                                                <h6>To Do ({{$todoSprintCount}})</h6>
                                                                <h5 class="todo-text">
                                                                    {{$todoSprintPercentage}} %
                                                                </h5>
                                                            @endif
                                                        </div>
                                                    <div class="col-md-4 sprint-stat stat-inprogress">
                                                        @if($activeSprint)
                                                                <?php $inprogressInSprint = App\Utils::getIssueCountInSprintByStatus($project->id, $activeSprint->id, 'inprogress');
                                                                $inprogressSprintCount = $inprogressInSprint['count'];
                                                                $inprogressSprintPercentage = $inprogressInSprint['percentage'];
                                                                ?>
                                                                    <h6>Progress ({{$inprogressSprintCount}})</h6>
                                                                    <h5 class="inprogress-text">
                                                                        {{$inprogressSprintPercentage}} %
                                                                    </h5>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4 sprint-stat stat-complete">
                                                        @if($activeSprint)
                                                                <?php $completeInSprint = App\Utils::getIssueCountInSprintByStatus($project->id, $activeSprint->id, 'complete');
                                                                $completeSprintCount = $completeInSprint['count'];
                                                                $completeSprintPercentage = $completeInSprint['percentage'];
                                                                ?>
                                                                    <h6>Complete ({{$completeSprintCount}})</h6>
                                                                    <h5 class="complete-text">
                                                                        {{$completeSprintPercentage}} %
                                                                    </h5>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row container-fluid">
                                            <hr/>
                                            <div class="project-card-actions">
                                                <span class="small">
                                                    <a href="/projects/{{$project->id}}/edit">Edit</a>
                                                </span>
                                            </div>
                                            <div class="project-card-stats">
                                                @include('projects.list.card-stats')
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
            @else
                <h2>No projects found</h2>
                <p><a href="/projects/create">Create a new project</a></p>
        </div>
    @endif
@endsection