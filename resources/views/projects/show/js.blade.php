@section('beforebodyend')
<script>
    jQuery(document).ready(function($) {
        
        var app = {};
        app.sort = {};
        
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
            var projectId = $('#project-name').attr('data-id');


            if(draggedFromListId !== draggedToListId)
            {
                // strikethrough on-the-fly if dropped into the complete list
                if(draggedToListId === 'complete')
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
            
            // When an issue is dragged and dropped into same sprint
            // @todo copy paste from plan/js.blade.php 
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
        @include('common-js.add-issues-js')
    });
</script>
@endsection