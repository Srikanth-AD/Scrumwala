@extends('app')
@section('content')
    <div class="col-md-8">
        <h2>Issues</h2>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Type</th>
                <th>Status</th>
                <th>Project</th>
                <th>Sprint</th>
            </tr>
            </thead>
            <tbody>
            @foreach($issues as $issue)
                <tr>
                    <td><a href="/issues/{{$issue->id}}">{{$issue->id}}</a></td>
                    <td><a href="/issues/{{$issue->id}}">{{$issue->title}}</a></td>
                    <td>{{$issue->statusLabel}}</td>
                    <td>{{$issue->typeLabel}}</td>
                    <td>{{$issue->projectName}}</td>
                    <td>{{App\Sprint::findOrFail($issue->sprint_id)->name}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <?php echo $issues->render() ?>
    </div>
@endsection
