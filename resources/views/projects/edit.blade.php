@extends('app')

@section('content')
<div class="container-fluid col-md-4">    
    <h1>Edit Project: {!! $project->name !!} </h1>
    
    {!! Form::model($project, ['method' =>'PATCH','action' => ['ProjectsController@update',$project->id]]) !!}
        <div class="form-group">
            {!! Form::label('name', 'Name:') !!}
            {!! Form::text('name', null, ['class' => 'form-control']) !!}
            <input name="project_id" type="hidden" value="{{$project->id}}">
        </div>

        <div class="form-group">
                {!! Form::label('type', 'Type:') !!}
                {!! Form::select('type', \App\Project::$projectTypes, null, ['placeholder' => 'Pick a type...']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('slug', 'Slug:') !!}
            {!! Form::text('slug', null, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('issue_prefix', 'Issue Prefix:') !!}
            {!! Form::text('issue_prefix', null, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('deadline', 'Deadline:') !!}
            {!! Form::input('date', 'deadline', $deadline, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::submit('Update Project', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>
@include('errors.list')
@endsection