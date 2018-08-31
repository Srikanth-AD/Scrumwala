@if (Session::has('issueUpdate'))
<div class="alert alert-info alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert"
            aria-label="Close"><span aria-hidden="true">&times;</span>
    </button>
    Issue: {{ Session::get('issueUpdate') }} has been updated.
</div>
@endif
