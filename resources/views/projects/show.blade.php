@extends('app')

@section('notifications')
    @if (Session::has('issueUpdate'))
        <div class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"
                    aria-label="Close"><span aria-hidden="true">&times;</span>
            </button>
            Issue: {{ Session::get('issueUpdate') }} has been updated.
        </div>
    @endif
@endsection

@section('content')
    
    @if($project)
        @include('projects.show.header')
        <div class="row container-fluid project-work main-content">
            @if($sprint)
                @if(count($issues) > 0)
                    @foreach($issueStatuses as $issueStatus)
                        <div class="col-sm-4">
                            <?php $list = App\Utils::getIssuesInSprintByIssueStatus($issueStatus->machine_name,$sprint->id) ?>
                            <h3 data-status-heading="{{$issueStatus->machine_name}}">{{$issueStatus->label}}
                                <span class="grey issue-count">({{$list->count()}})</span>
                            </h3>
                            @include('projects.show.issues-list')
                        </div>
                    @endforeach
                @else
                    <h3>No issues found</h3>
                    <p><a href="/issues/create">Create a new issue</a></p>
                @endif
            @else
                <p>No active sprint is set for this project.</p>
            @endif
        </div>
    @endif
@endsection
@include('projects.show.js')