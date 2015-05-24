@section('beforebodyend')
<script>
    jQuery(document).ready(function($) {

        /* Process Ajax request when a user updates an issue <-> sprint association via drag & drop */
        $(function() {
            $( ".sprint-list" ).sortable({
                connectWith:".connectedSortable",
                stop: function( event, ui ) {}
            }).disableSelection();
        });
        $(".sprint-list").on("sortstop", function(event,ui)
        {
            var draggedFromListId = $(this)[0].id;
            var draggedToListId = ui.item[0].parentElement.id;
            var issueId = $(ui.item[0]).attr('data-id');
            var projectId = $('#project-name').attr('data-id');
            
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
                        '_token': "{{ csrf_token() }}"
                    },
                    success: function(result) {
                        // Update issue counts for sprints - dragged from & to
                        $('.sprint-name[data-machine-name=' + draggedFromListId + '] > span.issue-count')
                                .text('(' + $('#' + draggedFromListId + ' li').length + ')');
                        $('.sprint-name[data-machine-name=' + draggedToListId + '] > span.issue-count')
                                .text('(' + $('#' + draggedToListId + ' li').length + ')');
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
        });

        // Display the inline form for adding an issue
        $("#action-add-issue").on('click', function() {
            $("#action-add-issue-body").show();
        });

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
                        '_token': "{{ csrf_token() }}"
                    },
                    success: function(result) {
                        $('li[data-id=' + issueId + ']').fadeOut('slow');
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
                        $('#' + result.sprintMachineName).fadeOut(4000);
                        $('.sprint-complete').fadeOut(4000);
                        $('.sprint-header[data-machine-name=' + result.sprintMachineName + ']').fadeOut(4000);
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