<div class="pull-right">
    <ul class="list-inline">
        <li>
            <span class="grey">Project Stats:</span>
        </li>
        <li>
            <span class="card-stat-icon">
                <img title="To Do" alt="To Do" width="18" height="18" src="{{asset('css/icons/ic_check_box_outline_blank_grey600_36dp.png')}}"/>
                {{$todoCount['count']}}
            </span>
        </li>
        <li>
            <span class="card-stat-icon">
                <img alt="In Progress" title="In Progress" width="18" height="18" src="{{asset('css/icons/ic_create_grey600_36dp.png')}}" />
                {{$inprogressCount['count']}}
            </span>
        </li>
        <li>
            <span class="card-stat-icon">
                <img alt="Complete" title="Complete"  width="18" height="18" src="{{asset('css/icons/ic_done_grey600_36dp.png')}}" />
                {{$completeCount['count']}}
            </span>
        </li>
    </ul>
</div>