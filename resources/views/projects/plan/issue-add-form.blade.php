<?php $issueTypes = App\IssueType::lists('label','id'); ?>

{!! Form::model($issue = new \App\Issue,['url' => 'issues/quickAdd', 'class' => 'form']) !!}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <input type="hidden" name="project_id" value="{{$project->id}}">

    <div class="form-group">
        {!! Form::label('title', 'Title:') !!}
        {!! Form::text('title', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('type_id', 'Type:') !!}
        {!! Form::select('type_id', $issueTypes, null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::submit('Add Issue', ['class' => 'btn btn-primary']) !!}
    </div>
{!! Form::close() !!}