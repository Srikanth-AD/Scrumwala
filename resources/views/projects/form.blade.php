<div class="form-group">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('slug', 'Slug:') !!}
    {!! Form::text('slug', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
        {!! Form::label('deadline', 'Deadline:') !!}
        {!! Form::input('date', 'deadline', $deadline, ['class' => 'form-control']) !!}
</div>
    
<div class="form-group">
        {!! Form::submit($submitButtonText, ['class' => 'btn btn-primary']) !!}
</div>