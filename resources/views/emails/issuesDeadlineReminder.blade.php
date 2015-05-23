@if($issuesWithDeadlineWithinADay)
    <p>Below are the issues with deadline within a day</p>
    @if($issuesWithDeadlineWithinADay)
        <table class="table-responsive table-striped">
            <thead>
                <tr>
                    <th width="20%">#</th>
                    <th  width="50%">Title</th>
                    <th  width="30%">Deadline</th>
                </tr>
            </thead>
            <tbody>
                @foreach($issuesWithDeadlineWithinADay as $issue)
                <tr>
                    <td><a href="{{URL::to('/')}}/issues/{{$issue->id}}">{{$issue->id}}</a></td>
                    <td><a href="{{URL::to('/')}}/issues/{{$issue->id}}">{{$issue->title}}</a></td>
                    <td>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $issue->deadline)->diffForHumans()}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>There are no issues with deadline within a day</p>
    @endif
@else
    <p>There are no issues with deadline within a day</p>
@endif