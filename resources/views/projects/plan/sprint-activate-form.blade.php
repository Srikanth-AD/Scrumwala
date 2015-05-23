{!! Form::model($sprint, ['method' =>'PATCH', 'url' => ['sprints/activate'],
'class' => 'form-inline sprint-activate-form']) !!}
<div class="form-group">
    <input name="machine_name" type="hidden" value="{{$sprint->machine_name}}">
    <input name="project_id" type="hidden" value="{{$project->id}}">
    {!! Form::text('name', $sprint->name, ['class' => 'form-control']) !!}
</div>
<div class="form-group">
    {!! Form::label('from_date', 'From:') !!}
    {!! Form::input('date', 'from_date', date('Y-m-d'), ['class' => 'form-control']) !!}
</div>
<div class="form-group">
    {!! Form::label('to_date', 'To:') !!}
    {!! Form::input('date', 'to_date', null, ['class' => 'form-control', 'required']) !!}
</div>
<div class="form-group">
    {!! Form::submit('Activate Sprint', ['class' => 'btn btn-primary']) !!}
</div>
{!! Form::close() !!}