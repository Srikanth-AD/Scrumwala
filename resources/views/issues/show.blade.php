@extends('app')
@section('content')
    @if($issue)
        <div class="container-fluid col-md-4">
            <div class="actions">
                @if(App\IssueStatus::find($issue->status_id)->machine_name != 'archive')
                    <button type="button" id="archive-issue" class="btn btn-default">Archive</button>
                @endif
            </div>
            <div class="clearfix container-fluid main-content">
                <h3 id="issue-title" data-id="{{$issue->id}}">{{$issue->title}}</h3>
                <a href="/issues/{{$issue->id}}/edit">Edit</a>
                <p>Description: {{$issue->description}}</p>
                <p>Status: {{App\IssueStatus::find($issue->status_id)->label}}</p>
                <?php $issueDeadline = $issue->deadline; ?>
                @if($issueDeadline)
                    Deadline:   {{$issueDeadline->year}}-{{$issueDeadline->month}}-{{$issueDeadline->day}}
                @endif
                <p>Project: {{App\Project::find($issue->project_id)->name}}</p>
            </div>
            <div id="action-messages" class="container-fluid">
            </div>
        </div>
    @endif
@endsection

@section('beforebodyend')
    <script>
        jQuery('#archive-issue').on('click', function() {
            var issueId = jQuery.trim(jQuery('#issue-title').attr('data-id'));
            jQuery.ajax({
                type: "POST",
                cache: false,
                url: "/issues/statuschange",
                data: {
                    'issueId': issueId,
                    'machineNameOfNewIssueStatus':'archive',
                    '_token': "{{ csrf_token() }}"
                },
                success: function(result) {
                    jQuery('#action-messages').addClass('alert alert-info').append(result);
                    setTimeout(function(){
                        jQuery('#action-messages').fadeOut(1000);
                    }, 4000);

                }
            });
        });
    </script>
@endsection