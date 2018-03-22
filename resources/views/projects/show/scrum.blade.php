@extends('app')

@section('notifications')
    @include('projects.show.issues-notifications')
@endsection

@section('content')
    @if($project)
        @include('projects.show.header')
        <div class="row container-fluid project-work main-content">
            @if($sprint)
                @if($numIssues > 0)
                    @foreach((array)$issueList as $status => $issues)
                        <div class="col-sm-4">
                            <h3 data-status-heading="{{$status}}">{{$status}}
                                <span class="grey issue-count">({{$issues->count()}})</span>
                            </h3>
                            @include('projects.show.issues-list')
                        </div>
                    @endforeach
                @else
                    <h3>No issues found</h3>
                    <p><a href="/issues/create">Create a new issue</a></p>
                @endif
            @else
                <h3>No active sprint is set for this project.</h3>
                <p><a class="btn btn-primary" href="/projects/{{$project->id}}/plan">Plan project</a></p>
            @endif
        </div>
    @endif
@endsection

@include('projects.show.js')