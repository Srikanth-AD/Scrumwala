@section('beforebodyend')
<script>
    jQuery(document).ready(function($) {

        var app = {};
        app.sort = {};

        /* Process Ajax request when a user updates an issue <-> sprint association via drag & drop */
        $(function() {
            $( ".sprint-list" ).sortable({
                connectWith:".connectedSortable",
                stop: function( event, ui ) {}
            }).disableSelection();
        });

        $(".sprint-list").on("sortstart", function(event,ui)
        {
            app.sort.issueId = $(ui.item[0]).attr('data-id');
            app.sort.currentNextIssueId = $('li[data-id=' + app.sort.issueId + ']').next().attr('data-id');
            app.sort.currentPrevIssueId = $('li[data-id=' + app.sort.issueId + ']').prev().attr('data-id');

        });

        $(".sprint-list").on("sortstop", function(event,ui)
        {
            var draggedFromListId = $(this)[0].id;
            var draggedToListId = ui.item[0].parentElement.id;
            var issueId = $(ui.item[0]).attr('data-id');
            var projectId = $('#project-name').attr('data-id');

            // When an issue is dragged and dropped into a different sprint
            if(draggedFromListId !== draggedToListId)
            {
                $.ajax({
                    type: "POST",
                    cache: false,
                    url: "/issues/sprintchange",
                    data: {
                        'issueId': issueId,
                        'machineNameOfNewSprint':draggedToListId,
                        'projectId':projectId,
                        'nextIssueId': $('li[data-id=' + issueId + ']').next().attr('data-id'),
                        'prevIssueId': $('li[data-id=' + issueId + ']').prev().attr('data-id'),
                        '_token': "{{ csrf_token() }}"
                    },
                    success: function(result) {
                        // Update issue counts for sprints - dragged from and to
                        $('.sprint-name[data-machine-name=' + draggedFromListId + '] > span.issue-count')
                                .text('(' + $('#' + draggedFromListId + ' li').length + ')');
                        $('.sprint-name[data-machine-name=' + draggedToListId + '] > span.issue-count')
                                .text('(' + $('#' + draggedToListId + ' li').length + ')');
                    }
                });
            }

            // When an issue is dragged and dropped into same sprint
            if(draggedFromListId === draggedToListId)
            {
                $.ajax({
                    type: "POST",
                    cache: false,
                    url: "/issues/priorityorder",
                    data: {
                        'issueId':issueId,
                        'machineNameOfSprint':draggedToListId,
                        'projectId':projectId,
                        'currentPrevIssueId':app.sort.currentPrevIssueId,
                        'currentNextIssueId':app.sort.currentNextIssueId,
                        'newNextIssueId': $('li[data-id=' + issueId + ']').next().attr('data-id'),
                        'newPrevIssueId': $('li[data-id=' + issueId + ']').prev().attr('data-id'),
                        '_token': "{{ csrf_token() }}"
                    },
                    success: function(result) {
                        // @todo display a notification?
                    }
                });
            }
        });

        /* When the "Activate" button is clicked, display the inline form to activate a sprint */
        $("button.sprint-activate").on('click', function() {
            $(this).next('.sprint-activate-form').fadeIn();
        });

        // Display the inline form for adding a sprint
        $("#action-add-sprint").on('click', function() {
            $("#action-add-sprint-body").fadeIn().show();
            $("#action-add-sprint-body form").find("input").filter(":visible").first().focus();
        });

        @include('common-js.add-issues-js')

        // When the close icon is clicked, close the parent section (for inline forms)
        $(".close").on('click', function() {
            $(this).parent().parent().fadeOut();
        })

        // Archive an issue
        $('.archive-issue').on('click', function() {
            if (confirm('Are you sure?')) {
                var issueId = $.trim($(this).parents('li').attr('data-id'));
                $.ajax({
                    type: "POST",
                    cache: false,
                    url: "/issues/statuschange",
                    data: {
                        'issueId': issueId,
                        'machineNameOfNewIssueStatus':'archive',
                        'nextIssueId': $('li[data-id=' + issueId + ']').next().attr('data-id'),
                        'prevIssueId': $('li[data-id=' + issueId + ']').prev().attr('data-id'),
                        '_token': "{{ csrf_token() }}"
                    },
                    success: function(result) {
                        $('li[data-id=' + issueId + ']').fadeOut('slow');
                        var sprintMachineName = $('li[data-id=' + issueId + ']').parent().attr('id');
                        var issueCountInSprint = $('#' + sprintMachineName + ' li:visible').length - 1;
                        $('.sprint-name[data-machine-name=' + sprintMachineName + '] > span.issue-count')
                                .text('(' + issueCountInSprint + ')');
                    }
                });
            }
        });

        // Complete a sprint
        $('.sprint-complete').on('click', function() {
            $.ajax({
                type: "POST",
                cache: false,
                url: "/sprints/complete",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'projectId':$.trim($(this).attr('data-project-id')),
                    'sprintMachineName': $.trim($(this).attr('data-id'))
                },
                success: function(result) {
                    if(result.status === 1)
                    {
                        $('#' + result.sprintMachineName).fadeOut(3500);
                        $('.sprint-complete').fadeOut(35000);
                        $('.sprint-header[data-machine-name=' + result.sprintMachineName + ']').fadeOut(3500);
                    }
                    else {
                        //
                    }
                    $('body').append('<div title="Please Note" id="sprint-complete-request-message">' + result.message + '</div>');
                    $(function() {
                        $("#sprint-complete-request-message").dialog();
                      });
                }
            });
        });


        // Toggle: show/hide sprint
        $(".toggle").on("click", function() {
          var listId = $(this).parent('h3').attr('data-machine-name');
          $("#" + listId).slideToggle();
        });

    });
</script>
@endsection