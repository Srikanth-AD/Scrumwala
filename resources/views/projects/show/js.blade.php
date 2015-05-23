@section('beforebodyend')
<script>
    jQuery(document).ready(function() {
        jQuery(function() {
            jQuery( "#todo, #inprogress, #complete" ).sortable({
                connectWith: ".connectedSortable",
                stop: function( event, ui ) {}
            }).disableSelection();
        });
        jQuery("#todo, #inprogress, #complete").on("sortstop", function(event,ui)
        {
            var draggedFromListId = jQuery(this)[0].id;
            var draggedToListId = ui.item[0].parentElement.id;
            var issueId = jQuery(ui.item[0]).attr('data-id');

            if(draggedFromListId !== draggedToListId)
            {
                // strikethrough on-the-fly if dropped into the complete list
                if(draggedToListId == 'complete')
                {
                    jQuery(ui.item[0]).css('text-decoration', 'line-through');
                } else {
                    jQuery(ui.item[0]).css('text-decoration', 'none');
                }

                jQuery.ajax({
                    type: "POST",
                    cache: false,
                    url: "/issues/statuschange",
                    data: {
                        'issueId': issueId,
                        'machineNameOfNewIssueStatus':draggedToListId,
                        '_token': "{{ csrf_token() }}"
                    },
                    success: function(result) {
                        jQuery(':header[data-status-heading=' + draggedFromListId + '] > span.issue-count')
                                .text('(' + jQuery('#' + draggedFromListId).children('li').length + ')');
                        jQuery(':header[data-status-heading=' + draggedToListId + '] > span.issue-count')
                                .text('(' + jQuery('#' + draggedToListId).children('li').length + ')');

                    }
                });
            }
        });
    });
</script>
@endsection