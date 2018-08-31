// Display the inline form for adding an issue
var dWidth = $(window).width() > 640 ? 600 : 'auto'
$("#action-add-issue-body-dialog").dialog({
    autoOpen: false,
    title: "Add Issue",
    modal: true,
    width: dWidth,
    minWidth: 320,
    maxWidth: 640,
    show: {
      effect: "fade",
      duration: 500
    },
});
$("#action-add-issue").on('click', function() {
    $("#action-add-issue-body-dialog").dialog("open");
});
