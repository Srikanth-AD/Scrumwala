@section('beforebodyend')
<script>
    jQuery(document).ready(function() {

        /* Process Ajax request when a user updates an issue <-> sprint association via drag & drop */
        jQuery(function() {
            jQuery( ".sprint-list" ).sortable({
                connectWith:".connectedSortable",
                stop: function( event, ui ) {}
            }).disableSelection();
        });
        jQuery(".sprint-list").on("sortstop", function(event,ui)
        {
            var draggedFromListId = jQuery(this)[0].id;
            var draggedToListId = ui.item[0].parentElement.id;
            var issueId = jQuery(ui.item[0]).attr('data-id');
            var projectId = jQuery('#project-name').attr('data-id');
            
            if(draggedFromListId !== draggedToListId)
            {
                jQuery.ajax({
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
                        jQuery('.sprint-name[data-machine-name=' + draggedFromListId + '] > span.issue-count')
                                .text('(' + jQuery('#' + draggedFromListId + ' li').length + ')');
                        jQuery('.sprint-name[data-machine-name=' + draggedToListId + '] > span.issue-count')
                                .text('(' + jQuery('#' + draggedToListId + ' li').length + ')');
                    }
                });
            }
        });

        /* When the "Activate" button is clicked, display the inline form to activate a sprint */
        jQuery("button.sprint-activate").on('click', function() {
            jQuery(this).next('.sprint-activate-form').fadeIn();
        });

        // Display the inline form for adding a sprint
        jQuery("#action-add-sprint").on('click', function() {
            jQuery("#action-add-sprint-body").fadeIn().show();
        });

        // Archive an issue
        jQuery('.archive-issue').on('click', function() {
            if (confirm('Are you sure?')) {
                var issueId = jQuery.trim(jQuery(this).parents('li').attr('data-id'));
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
                        jQuery('li[data-id=' + issueId + ']').fadeOut('slow');
                    }
                });
            }
        });

        // Complete a sprint
        jQuery('.sprint-complete').on('click', function() {
            jQuery.ajax({
                type: "POST",
                cache: false,
                url: "/sprints/complete",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'projectId':jQuery.trim(jQuery(this).attr('data-project-id')),
                    'sprintMachineName': jQuery.trim(jQuery(this).attr('data-id'))
                },
                success: function(result) {
                    if(result.status === 1)
                    {
                        jQuery('#' + result.sprintMachineName).fadeOut(4000);
                        jQuery('.sprint-complete').fadeOut(4000);
                        jQuery('.sprint-header[data-machine-name=' + result.sprintMachineName + ']').fadeOut(4000);
                    } 
                    else {
                        //
                    }
                    jQuery('body').append('<div title="Please Note" id="sprint-complete-request-message">' + result.message + '</div>');
                    jQuery(function() {
                        jQuery("#sprint-complete-request-message").dialog();
                      });                    
                }
            });
        });

        jQuery(".toggle").on("click", function() { 
          var listId = jQuery(this).parent('h3').attr('data-machine-name');
          jQuery("#" + listId).slideToggle();
        });

    });
</script>
@endsection