@extends('app') 
@section('notifications')
    @include('projects.show.issues-notifications')
@endsection
 
@section('content')
    @if($project)
        @include('projects.show.header')
        <div class="row container-fluid project-work main-content">
            @if(count($numIssues) > 0) @foreach($issueList as $status=>$issues)
            <div class="col-sm-4">
                <h3 data-status-heading="{{$status}}">{{$status}}
                    <span class="grey issue-count">({{count($issues)}})</span>
                </h3>
            @include('projects.show.issues-list')
            </div>
            @endforeach @else
            <h3>No issues found</h3>
            <p><a href="/issues/create">Create a new issue</a></p>
            @endif
        </div>
        <!-- modal dialog -->
        <div class="col-md-12" id="action-add-issue-body-dialog">
            @include('projects.plan.issue-add-form')
        </div>
    @endif
@endsection

@include('projects.show.js')