@extends('app')

@section('content')
<div class="container-fluid col-md-4">    
    <h1>Create an issue</h1>
    {!! Form::model($issue = new \App\Issue,['url' => 'issues']) !!}
    <div class="form-group">
        {!! Form::label('title', 'Title:') !!}
        {!! Form::text('title', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('description', 'Description:') !!}
        {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('project_id', 'Project:') !!}
        {!! Form::select('project_id', $projectNames, null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('deadline', 'Deadline:') !!}
        {!! Form::input('date', 'deadline', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('type_id', 'Type:') !!}
        {!! Form::select('type_id', $issueTypeLabels, null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::submit('Create Issue', ['class' => 'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}
    
</div>
@include('errors.list')
@stop