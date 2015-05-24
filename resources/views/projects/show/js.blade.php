@section('beforebodyend')
<script>
    jQuery(document).ready(function($) {
        $(function() {
            $( "#todo, #inprogress, #complete" ).sortable({
                connectWith: ".connectedSortable",
                stop: function( event, ui ) {}
            }).disableSelection();
        });
        $("#todo, #inprogress, #complete").on("sortstop", function(event,ui)
        {
            var draggedFromListId = $(this)[0].id;
            var draggedToListId = ui.item[0].parentElement.id;
            var issueId = $(ui.item[0]).attr('data-id');

            if(draggedFromListId !== draggedToListId)
            {
                // strikethrough on-the-fly if dropped into the complete list
                if(draggedToListId == 'complete')
                {
                    $(ui.item[0]).css('text-decoration', 'line-through');
                } else {
                    $(ui.item[0]).css('text-decoration', 'none');
                }

                $.ajax({
                    type: "POST",
                    cache: false,
                    url: "/issues/statuschange",
                    data: {
                        'issueId': issueId,
                        'machineNameOfNewIssueStatus':draggedToListId,
                        '_token': "{{ csrf_token() }}"
                    },
                    success: function(result) {
                        $(':header[data-status-heading=' + draggedFromListId + '] > span.issue-count')
                                .text('(' + $('#' + draggedFromListId).children('li').length + ')');
                        $(':header[data-status-heading=' + draggedToListId + '] > span.issue-count')
                                .text('(' + $('#' + draggedToListId).children('li').length + ')');

                    }
                });
            }
        });
    });
</script>
@endsection