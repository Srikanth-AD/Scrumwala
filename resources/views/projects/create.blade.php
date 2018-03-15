@extends('app')

@section('content')
<div class="container-fluid col-md-4">    
    <h1>Create a project</h1>
    {!! Form::model($project = new \App\Project, ['url' => 'projects']) !!}
        <div class="form-group">
            {!! Form::label('name', 'Name:') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
        </div>

        <div class="form-group">
                {!! Form::label('type', 'Type:') !!}
                {!! Form::select('type', \App\Project::$projectTypes, null, ['placeholder' => 'Pick a type...']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('slug', 'Slug:') !!}
            {!! Form::text('slug', null, ['class' => 'form-control','required']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('issue_prefix', 'Issue Prefix:') !!}
            {!! Form::text('issue_prefix', null, ['class' => 'form-control', 'required']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('deadline', 'Deadline:') !!}
            {!! Form::input('date', 'deadline', null, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::submit('Create Project', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
    
</div>
@include('errors.list')
@stop