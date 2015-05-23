
@if ($errors->any())
<div class="clearfix container-fluid col-md-4">
    <ul class="alert alert-danger">
         @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
    </ul>
</div>
@endif