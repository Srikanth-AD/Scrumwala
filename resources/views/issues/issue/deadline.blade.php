@if($issue->deadline)
    <div class="row">
        <div class="col-md-6 pull-right">
            <img width="18" height="18" src="{{asset('css/icons/ic_schedule_black_36dp.png')}}"/>
            <span>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $issue->deadline)->diffForHumans()}}</span>
        </div>
    </div>
@endif