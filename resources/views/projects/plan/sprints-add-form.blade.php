{!! Form::model($sprint = new \App\Sprint, ['url' => 'sprints/add', 'class' => 'form-inline pull-left']) !!}
<div class="form-group">
    {!! Form::text('name', null, ['placeholder' => 'Sprint Name', 'class' => 'form-control']) !!}
    <input name="project_id" type="hidden" value="{{$project->id}}">
</div>
<div class="form-group">
    {!! Form::submit('Add Sprint', ['class' => 'btn btn-primary']) !!}
</div>
{!! Form::close() !!}